<script setup>
import { router } from '@inertiajs/vue3'
import IssueCoverCard from '@/Components/IssueCoverCard.vue'

const props = defineProps({
    issue: Object,
    publications: Array,
})

const goPublication = (id) => router.get(route('publications.detail', id))
const goAuthor = (id) => router.get(route('authors.detail', id))
</script>

<template>
    <div class="max-w-4xl mx-auto px-4 py-10">

        <!-- HEADER -->
        <div class="flex flex-col sm:flex-row gap-8 mb-12">

            <!-- Ľavá strana - info -->
            <div class="flex flex-col justify-center gap-3 flex-1">
                <h1 class="text-4xl font-bold text-[#1E4E8C]">
                    OMFI {{ issue.year }}/{{ issue.number }}
                </h1>
                <div class="flex flex-col gap-1 text-gray-500 text-sm">
                    <span>Volume {{ issue.volume }}</span>
                    <span>Rok {{ issue.year }}</span>
                    <span v-if="issue.published_at">Vydané: {{ issue.published_at }}</span>
                </div>

                <!-- PDF button -->
                <div class="mt-2">
                    <a
                        v-if="issue.pdf_path"
                        :href="`/storage/${issue.pdf_path}`"
                        target="_blank"
                        class="inline-flex items-center gap-2 bg-[#1E4E8C] hover:bg-[#163d70] text-white text-sm font-semibold px-5 py-2.5 rounded-lg transition"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17v3a1 1 0 001 1h16a1 1 0 001-1v-3" />
                        </svg>
                        Stiahnuť celé číslo (PDF)
                    </a>
                    <span
                        v-else
                        class="inline-flex items-center gap-2 bg-gray-100 text-gray-400 text-sm font-semibold px-5 py-2.5 rounded-lg cursor-not-allowed"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17v3a1 1 0 001 1h16a1 1 0 001-1v-3" />
                        </svg>
                        PDF nebolo nahraté
                    </span>
                </div>
            </div>

            <!-- Pravá strana - cover karta -->
            <div class="w-40 shrink-0 mx-auto sm:mx-0">
                <IssueCoverCard
                    :year="issue.year"
                    :issue="{ id: issue.id, number: issue.number, volume: issue.volume, hasPublication: true }"
                    image-url="/images/hero-omfi2.jpg"
                />
            </div>
        </div>

        <!-- ZOZNAM PUBLIKÁCIÍ -->
        <h2 class="text-xl font-bold text-gray-700 mb-4 uppercase tracking-wide">Contents</h2>
        <hr class="mb-6 border-gray-200" />

        <div v-if="publications.length" class="flex flex-col divide-y divide-gray-100">
            <div
                v-for="pub in publications"
                :key="pub.id"
                class="py-5 flex items-start justify-between gap-4"
            >
                <div class="flex-1">
                    <button
                        type="button"
                        @click="goPublication(pub.id)"
                        class="text-left text-[#1E4E8C] font-semibold uppercase text-sm tracking-wide hover:underline leading-snug"
                    >
                        {{ pub.title }}
                    </button>
                    <div class="mt-1 text-sm text-gray-600">
                        <span v-for="(author, i) in pub.authors" :key="author.id">
                            <button
                                type="button"
                                @click="goAuthor(author.id)"
                                class="hover:underline hover:text-[#1E4E8C] transition"
                            >{{ author.firstname }} {{ author.surname }}</button><span v-if="i < pub.authors.length - 1">, </span>
                        </span>
                    </div>
                </div>
                <div class="shrink-0 text-sm text-gray-400 font-medium pt-0.5">
                    <span v-if="pub.firstpage && pub.firstpage !== '0'">
                        s. {{ pub.firstpage }}<span v-if="pub.lastpage && pub.lastpage !== '0'">–{{ pub.lastpage }}</span>
                    </span>
                </div>
            </div>
        </div>

        <p v-else class="text-center text-gray-500 py-10">
            Žiadne publikácie v tomto vydaní.
        </p>
    </div>
</template>