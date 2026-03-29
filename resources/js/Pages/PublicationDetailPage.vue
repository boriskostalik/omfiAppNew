<script setup>
import { computed, ref } from "vue";
import { router } from "@inertiajs/vue3";
import Bibtex from "@/Components/Bibtex.vue";
import RIS from "@/Components/RIS.vue";

const props = defineProps({
    publication: { type: Object, required: true },
});

const isBibtexVisible = ref(false);
const isRISVisible = ref(false);

const goAuthor = (id) => router.get(route("authors.detail", id));
const goIssue = (id) => router.get(route("archive.issue", id));

const pdfUrl = computed(() => {
    return (
        props.publication?.pdf_url ||
        props.publication?.pdfUrl ||
        props.publication?.pdf ||
        null
    );
});
</script>

<template>
    <div class="max-w-4xl mx-auto px-4 py-14">
        <div class="mb-16">
            <h1 class="text-5xl font-bold text-gray-900 leading-snug mb-4">
                {{ publication.title }}
            </h1>
            <div class="flex flex-col gap-2 text-gray-500 text-lg">
                <span v-if="publication.issue">
                    Vydanie:
                    <button
                        type="button"
                        @click="goIssue(publication.issue.id)"
                        class="hover:underline hover:text-gray-900 transition"
                    >OMFI {{ publication.issue.year }}/{{ publication.issue.number }}</button>
                </span>
                <span v-if="publication.firstpage && publication.firstpage !== '0'">
                    Strany: {{ publication.firstpage }}–{{ publication.lastpage }}
                </span>
                <span v-if="publication.type">Typ: {{ publication.type }}</span>
                <span v-if="publication.doi">
                    DOI:
                    <a
                        :href="`https://doi.org/${publication.doi}`"
                        target="_blank"
                        class="hover:underline hover:text-gray-900 transition"
                    >{{ publication.doi }}</a>
                </span>
            </div>
        </div>

        <div v-if="publication.abstract" class="mb-8">
            <h2 class="text-sm font-bold uppercase tracking-wide text-gray-400 mb-3">
                Abstrakt
            </h2>
            <p class="text-gray-700 leading-relaxed text-lg">
                {{ publication.abstract }}
            </p>
        </div>

        <div v-if="publication.keywords?.length" class="mb-8">
            <h2 class="text-sm font-bold uppercase tracking-wide text-gray-400 mb-3">
                Kľúčové slová
            </h2>
            <p class="text-gray-600 text-lg">{{ publication.keywords.map(k => k.name).join(', ') }}</p>
        </div>

        <div class="flex flex-wrap gap-3 mb-8">
            <a
                v-if="pdfUrl"
                :href="pdfUrl"
                target="_blank"
                class="inline-flex items-center gap-2 bg-gray-900 hover:bg-gray-700 text-white font-semibold px-6 py-3 rounded-lg transition text-base"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17v3a1 1 0 001 1h16a1 1 0 001-1v-3" />
                </svg>
                Stiahnuť PDF
            </a>
            <button
                type="button"
                @click="isBibtexVisible = true"
                class="inline-flex items-center gap-2 border border-gray-300 hover:border-gray-900 hover:text-gray-900 text-gray-600 font-semibold px-6 py-3 rounded-lg transition text-base"
            >
                BibTeX
            </button>
            <button
                type="button"
                @click="isRISVisible = true"
                class="inline-flex items-center gap-2 border border-gray-300 hover:border-gray-900 hover:text-gray-900 text-gray-600 font-semibold px-6 py-3 rounded-lg transition text-base"
            >
                RIS
            </button>
        </div>

        <div v-if="publication.authors?.length">
            <h2 class="text-2xl font-bold text-gray-900 mb-5 uppercase tracking-wide">
                Autori
            </h2>
            <hr class="mb-8 border-gray-200" />
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
                            class="font-bold text-gray-900 hover:underline text-xl"
                        >
                            {{ author.firstname }} {{ author.surname }}<span v-if="author.pivot?.is_editor === 'Y'" class="text-gray-400 font-normal text-lg"> (editor)</span>
                        </button>
                        <p v-if="author.institute" class="text-lg text-gray-500 mt-0.5">
                            {{ author.institute }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <Bibtex
            :publication="publication"
            :visible="isBibtexVisible"
            @close="isBibtexVisible = false"
        />
        <RIS
            :publication="publication"
            :visible="isRISVisible"
            @close="isRISVisible = false"
        />
    </div>
</template>
