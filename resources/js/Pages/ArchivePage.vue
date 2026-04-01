<script setup>
import { router } from "@inertiajs/vue3";

const props = defineProps({
    years: Array,
    issuesByYear: Object,
});

const goIssue = (id) => router.get(route("archive.issue", id));
</script>

<template>
    <div class="relative h-[260px] w-full overflow-hidden bg-center bg-cover flex items-center" style="background-image: url('/images/hero1.png')">
        <div class="relative z-10 max-w-4xl mx-auto px-6 w-full">
            <div class="inline-block bg-black/70 px-3 py-1">
                <h1 class="text-6xl sm:text-7xl font-bold text-white leading-none tracking-tight">
                    Archív
                </h1>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-8 py-12">
        <div class="border-t border-gray-200">
            <div
                v-for="year in years"
                :key="year"
                class="flex items-baseline gap-8 sm:gap-12 py-5 border-b border-gray-100"
            >
                <div class="shrink-0 w-36">
                    <span class="text-xl font-bold text-[#1E4E8C] tabular-nums tracking-tight">{{ year }}</span>
                    <span v-if="(issuesByYear[year] ?? [])[0]?.volume" class="ml-2 text-sm text-gray-500 font-medium">Vol. {{ (issuesByYear[year])[0].volume }}</span>
                </div>
                <div class="flex flex-wrap gap-x-8 gap-y-2 items-baseline">
                    <button
                        v-for="issue in (issuesByYear[year] ?? [])"
                        :key="issue.id"
                        @click="goIssue(issue.id)"
                        class="text-base text-gray-700 hover:text-[#1E4E8C] hover:underline underline-offset-4 transition-colors"
                    >
                        Číslo {{ issue.number || "—" }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
