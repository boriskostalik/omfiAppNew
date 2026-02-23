<script setup>
import Card from 'primevue/card'
import Button from 'primevue/button'
import Message from 'primevue/message'
import Divider from 'primevue/divider'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
	publication: { type: Object, required: true },
	pdfUrl: { type: String, default: '' },
	pdfViewerSrc: { type: String, default: '' },
})

defineEmits(['show-bibtex', 'show-ris'])

const cleanTitle = (s) => (s ?? '').trim().replace(/[„“”"]/g, '').replace(/[‚‘’']/g, '')
const showEnglishTitle = () => {
	const sk = cleanTitle(props.publication?.title)
	const en = cleanTitle(props.publication?.title_eng)
	return !!en && en !== sk
}
</script>

<template>
	<Card class="mt-6">
		<template #title>
			<div>
				<div class="text-2xl font-semibold text-slate-900">{{ publication.title }}</div>
				<div v-if="showEnglishTitle()" class="text-sm text-slate-600 mt-1">{{ publication.title_eng }}</div>
			</div>
			<Divider />
		</template>

		<template #content>
			<div class="space-y-6">
				<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
					<div class="space-y-4">
						<div class="flex flex-col gap-3 text-base text-slate-700">
							<div v-if="publication.journal" class="flex gap-2">
								<strong class="text-slate-900">Časopis:</strong><span>{{ publication.journal }}</span>
							</div>

							<div v-if="publication.year && publication.number && (publication.issue_id || publication.issue?.id)" class="flex items-center gap-2">
								<strong class="text-slate-900">Vydanie:</strong>
								<Link :href="route('archive.issue', publication.issue_id ?? publication.issue.id)" class="inline-flex" @click.stop>
									<Button :label="`${publication.year}/${publication.number}`" severity="secondary" size="small" class="px-4 py-2 text-base" />
								</Link>
							</div>

							<div v-else class="flex flex-wrap gap-4">
								<div v-if="publication.year" class="flex gap-2"><strong class="text-slate-900">Rok:</strong><span>{{ publication.year }}</span></div>
								<div v-if="publication.number" class="flex gap-2"><strong class="text-slate-900">Vydanie:</strong><span>{{ publication.number }}</span></div>
								<div v-if="publication.volume" class="flex gap-2"><strong class="text-slate-900">Volume:</strong><span>{{ publication.volume }}</span></div>
							</div>

							<div v-if="publication.note" class="flex gap-2">
								<strong class="text-slate-900">Poznámka:</strong>
								<span class="whitespace-pre-line">{{ publication.note }}</span>
							</div>

							<div v-if="publication.firstpage && publication.lastpage" class="flex gap-2">
								<strong class="text-slate-900">Strany:</strong><span>{{ publication.firstpage }}–{{ publication.lastpage }}</span>
							</div>

							<div v-if="publication.doi" class="flex gap-2">
								<strong class="text-slate-900">DOI:</strong>
								<a :href="'https://doi.org/' + publication.doi" target="_blank" rel="noreferrer" class="text-blue-600 hover:underline" @click.stop>
									{{ publication.doi }}
								</a>
							</div>
						</div>

						<div class="flex flex-col sm:flex-row gap-2">
							<Button label="BibTeX" severity="secondary" class="sm:w-fit" @click="$emit('show-bibtex')" />
							<Button label="RIS" severity="secondary" class="sm:w-fit" @click="$emit('show-ris')" />
						</div>
					</div>

					<div>
						<div class="flex items-center justify-between gap-3">
							<h3 class="text-lg font-semibold text-slate-900">PDF</h3>
							<a v-if="pdfUrl" :href="pdfUrl" target="_blank" rel="noreferrer" class="text-sm text-blue-600 hover:underline">Otvoriť PDF</a>
						</div>

						<div v-if="pdfViewerSrc" class="mt-3">
							<div class="w-full rounded-xl border border-slate-200 bg-white overflow-hidden">
								<div class="px-4 py-2 border-b border-slate-200 bg-slate-50"><div class="text-sm text-slate-700">Náhľad PDF</div></div>
								<div class="w-full aspect-[1/1.414] md:aspect-[16/9] bg-slate-100">
									<object :data="pdfViewerSrc" type="application/pdf" class="w-full h-full">
										<iframe :src="pdfViewerSrc" class="w-full h-full" title="PDF viewer" />
									</object>
								</div>
							</div>
						</div>

						<Message v-else severity="info" :closable="false" class="mt-3 justify-items-center">PDF vydanie tejto publikácie nebolo nahrané.</Message>
					</div>
				</div>

				<div>
					<h3 class="text-lg font-semibold text-slate-900">Abstrakt</h3>
					<p class="text-slate-700 mt-2 whitespace-pre-line">{{ publication.abstract || '—' }}</p>
				</div>
			</div>
		</template>
	</Card>
</template>