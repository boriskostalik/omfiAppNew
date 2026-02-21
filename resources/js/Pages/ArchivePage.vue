<script setup>
import { computed } from 'vue'
import PanelMenu from 'primevue/panelmenu'
import { router } from '@inertiajs/vue3'

const props = defineProps({
	years: Array,
	issuesByYear: Object,
})

const goIssue = (id) => router.get(route('archive.issue', id))
const issueLabel = (y, iss) => {
	const num = iss?.number
	return num !== null && num !== undefined && num !== '' && num !== 0 ? `${y}/${num}` : `${y}/—`
}
const rowCells = (y) => {
	const arr = props.issuesByYear?.[y] ?? []
	return arr.map((iss) => ({ type: 'issue', iss }))
}
const items = computed(() =>
	(props.years ?? []).map((y) => ({
		key: String(y),
		label: String(y),
		items: (props.issuesByYear?.[y] ?? []).map((iss) => ({
			key: `i-${iss.id}`,
			label: issueLabel(y, iss),
			id: iss.id,
			volume: iss.volume,
		})),
	}))
)
</script>
<template>
	<div class="max-w-4xl mx-auto px-4 py-10">
		<div class="relative mb-12">
			<div class="h-[160px] w-full bg-center bg-cover rounded-lg" style="background-image:url('/images/hero-omfi2.jpg')"></div>
			<div class="absolute inset-0 flex items-center justify-center">
				<div class="bg-white/90 px-10 py-4 rounded-md shadow-sm">
					<h1 class="text-4xl sm:text-5xl font-normal text-black tracking-wide">Archív</h1>
				</div>
			</div>
		</div>
		<div class="md:hidden archive-panelmenu">
			<PanelMenu :model="items">
				<template #item="{ item }">
					<div v-if="item.items" class="w-full">
						<div class="w-full bg-[#1E4E8C] text-white font-bold flex items-center justify-center h-16">
							<span class="text-3xl leading-none">{{ item.label }}</span>
						</div>
					</div>
					<button
						v-else
						type="button"
						@click="goIssue(item.id)"
						class="w-full bg-white hover:bg-slate-50 transition flex flex-col items-center justify-center text-center border-t border-slate-300"
						style="height:64px;"
					>
						<div class="text-2xl font-semibold text-black leading-none">{{ item.label }}</div>
						<div v-if="item.volume" class="mt-1 text-sm text-slate-500 leading-none">Volume {{ item.volume }}</div>
					</button>
				</template>
			</PanelMenu>
		</div>
		<div class="mt-8 space-y-4 hidden md:block">
			<div v-for="y in props.years" :key="`d-${y}`" class="w-full">
				<div class="flex items-stretch gap-0">
					<div class="bg-[#1E4E8C] text-white font-bold flex items-center justify-center px-5" style="height:56px; min-width:100px;">
						<span class="text-2xl leading-none">{{ y }}</span>
					</div>
					<div class="flex-1 overflow-x-auto">
						<div class="inline-flex">
							<button
								v-for="cell in rowCells(y)"
								:key="cell.iss.id"
								type="button"
								@click="goIssue(cell.iss.id)"
								class="border border-slate-400 bg-white hover:bg-slate-50 transition px-5 flex items-center justify-center text-black"
								style="height:56px; min-width:140px;"
							>
								<span class="text-2xl leading-none">{{ issueLabel(y, cell.iss) }}</span>
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

