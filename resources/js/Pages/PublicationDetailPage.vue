<script setup>
import { computed, ref } from 'vue'
import { router } from '@inertiajs/vue3'
import Bibtex from '@/Components/Bibtex.vue'
import RIS from '@/Components/RIS.vue'

const props = defineProps({
    publication: { type: Object, required: true },
})

const isBibtexVisible = ref(false)
const isRISVisible = ref(false)

const goAuthor = (id) => router.get(route('authors.detail', id))
const goIssue = (id) => router.get(route('archive.issue', id))

const pdfUrl = computed(() => {
    return props.publication?.pdf_url || props.publication?.pdfUrl || props.publication?.pdf || null
})
</script>

<template>
    <div class="max-w-4xl mx-auto px-4 py-10">

        <!-- HEADER -->
        <div class="mb-8">
            <!-- Issue badge -->
            <button
                v-if="publication.issue"
                type="button"
                @click="goIssue(publication.issue.id)"
                class="inline-flex items-center gap-1 text-xs font-semibold text-[#1E4E8C] bg-blue-50 hover:bg-blue-100 px-3 py-1 rounded-full mb-4 transition"
            >
                OMFI {{ publication.issue.year }}/{{ publication.issue.number }}
            </button>

            <!-- Titulok -->
            <h1 class="text-3xl font-bold text-gray-900 leading-snug mb-4">
                {{ publication.title }}
            </h1>

            <!-- Autori -->
            <div class="flex flex-wrap gap-2 mb-6">
                <button
                    v-for="author in publication.authors"
                    :key="author.id"
                    type="button"
                    @click="goAuthor(author.id)"
                    class="text-sm text-[#1E4E8C] hover:underline font-medium"
                >
                    {{ author.firstname }} {{ author.surname }}
                </button>
            </div>

            <!-- Strany + typ -->
            <div class="flex flex-wrap gap-4 text-sm text-gray-500">
                <span v-if="publication.firstpage && publication.firstpage !== '0'">
                    Strany: {{ publication.firstpage }}–{{ publication.lastpage }}
                </span>
                <span v-if="publication.type">Typ: {{ publication.type }}</span>
                <span v-if="publication.doi">
                    DOI: <a :href="`https://doi.org/${publication.doi}`" target="_blank" class="text-[#1E4E8C] hover:underline">{{ publication.doi }}</a>
                </span>
            </div>
        </div>

        <hr class="border-gray-200 mb-8" />

        <!-- ABSTRACT -->
        <div v-if="publication.abstract" class="mb-8">
            <h2 class="text-sm font-bold uppercase tracking-wide text-gray-400 mb-3">Abstrakt</h2>
            <p class="text-gray-700 leading-relaxed">{{ publication.abstract }}</p>
        </div>

        <!-- KEYWORDS -->
        <div v-if="publication.keywords" class="mb-8">
            <h2 class="text-sm font-bold uppercase tracking-wide text-gray-400 mb-3">Kľúčové slová</h2>
            <p class="text-gray-600 text-sm">{{ publication.keywords }}</p>
        </div>

        <!-- AKCIE -->
        <div class="flex flex-wrap gap-3 mb-8">
            <a
                v-if="pdfUrl"
                :href="pdfUrl"
                target="_blank"
                class="inline-flex items-center gap-2 bg-[#1E4E8C] hover:bg-[#163d70] text-white text-sm font-semibold px-5 py-2.5 rounded-lg transition"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17v3a1 1 0 001 1h16a1 1 0 001-1v-3" />
                </svg>
                Stiahnuť PDF
            </a>

            <button
                type="button"
                @click="isBibtexVisible = true"
                class="inline-flex items-center gap-2 border border-gray-300 hover:border-[#1E4E8C] hover:text-[#1E4E8C] text-gray-600 text-sm font-semibold px-5 py-2.5 rounded-lg transition"
            >
                BibTeX
            </button>

            <button
                type="button"
                @click="isRISVisible = true"
                class="inline-flex items-center gap-2 border border-gray-300 hover:border-[#1E4E8C] hover:text-[#1E4E8C] text-gray-600 text-sm font-semibold px-5 py-2.5 rounded-lg transition"
            >
                RIS
            </button>
        </div>

        <!-- AUTORI DETAIL -->
        <div v-if="publication.authors?.length">
            <h2 class="text-sm font-bold uppercase tracking-wide text-gray-400 mb-4">O autoroch</h2>
            <div class="flex flex-col divide-y divide-gray-100">
                <div
                    v-for="author in publication.authors"
                    :key="author.id"
                    class="py-4 flex items-center justify-between"
                >
                    <div>
                        <button
                            type="button"
                            @click="goAuthor(author.id)"
                            class="font-semibold text-[#1E4E8C] hover:underline"
                        >
                            {{ author.firstname }} {{ author.surname }}
                        </button>
                        <p v-if="author.institute" class="text-sm text-gray-500 mt-0.5">{{ author.institute }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modaly -->
        <Bibtex :publication="publication" :visible="isBibtexVisible" @close="isBibtexVisible = false" />
        <RIS :publication="publication" :visible="isRISVisible" @close="isRISVisible = false" />
    </div>
</template>