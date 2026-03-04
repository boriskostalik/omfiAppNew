<script setup>
import { router } from '@inertiajs/vue3'

const props = defineProps({
	year: { type: [String, Number], required: true },
	issue: { type: Object, required: true },
	imageUrl: { type: String, default: '/images/hero-omfi.jpg' },
})

const go = () => {
	if (!props.issue?.hasPublication) return
	const issueId = props.issue?.id ?? props.issue?.issue_id
	if (!issueId) return
	router.get(route('archive.issue', issueId))
}
</script>

<template>
	<button type="button" @click="go" class="group w-full text-left" :disabled="!issue.hasPublication">
		<div
			class="relative aspect-[3/4] overflow-hidden rounded-none shadow-lg transition-shadow duration-300 group-hover:shadow-xl"
			:class="!issue.hasPublication ? 'opacity-30 cursor-not-allowed' : ''"
		>
			<div
				class="absolute inset-0 bg-cover bg-center transition-transform duration-300 group-hover:scale-[1.04]"
				:style="{ backgroundImage: `url('${imageUrl}')` }"
			></div>

			<div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>

			<div class="absolute inset-0 flex flex-col justify-between p-[8%]">
				<div class="text-[clamp(1.5rem,7cqi,3rem)] font-semibold tracking-wide text-[#1E4E8C] drop-shadow" >
          {{ year }} / {{ issue.number ?? '—' }}
        </div>  
				<div>
					<div class="text-[clamp(1.2rem,8cqi,3rem)] font-bold text-white drop-shadow leading-none">
						OMFI
					</div>
					<div v-if="!issue.hasPublication" class="text-[clamp(0.5rem,2cqi,0.75rem)] text-white/70 mt-1">
						Bez publikácií
					</div>
				</div>
			</div>
		</div>
	</button>
</template>