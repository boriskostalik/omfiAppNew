<script setup>
import { router } from "@inertiajs/vue3";
import Button from "primevue/button";

const props = defineProps({
    issue: Object,
    publications: Array,
});

const goPublication = (id) => router.get(route("publications.detail", id));
const goAuthor = (id) => router.get(route("authors.detail", id));
</script>

<template>
    <div class="max-w-4xl mx-auto px-4 py-14">
        <div class="mb-16">
            <div class="flex flex-col gap-4">
                <h1 class="text-5xl font-bold text-gray-900">
                    Vydanie OMFI {{ issue.year }}/{{ issue.number }}
                </h1>
                <div class="flex flex-col gap-2 text-gray-500 text-lg">
                    <span>Volume: {{ issue.volume }}</span>
                    <span>Rok: {{ issue.year }}</span>
                    <span v-if="issue.published_at"
                        >Vydané: {{ issue.published_at }}</span
                    >
                </div>
                <div class="mt-3">
                    <a
                        v-if="issue.pdf_path"
                        :href="`/storage/${issue.pdf_path}`"
                        target="_blank"
                        class="inline-flex items-center gap-2 bg-[#1E4E8C] hover:bg-[#184073] text-white font-semibold px-6 py-3 rounded-lg transition text-base"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="w-5 h-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3M3 17v3a1 1 0 001 1h16a1 1 0 001-1v-3"
                            />
                        </svg>
                        Otvoriť PDF vydania
                    </a>
                    <span
                        v-else
                        class="inline-flex items-center gap-2 bg-gray-100 text-gray-400 font-semibold px-6 py-3 rounded-lg cursor-not-allowed text-base"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="w-5 h-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3M3 17v3a1 1 0 001 1h16a1 1 0 001-1v-3"
                            />
                        </svg>
                        PDF nebolo nahraté
                    </span>
                </div>
            </div>
        </div>

        <h2
            class="text-2xl font-bold text-gray-900 mb-5 uppercase tracking-wide"
        >
            Obsah
        </h2>
        <hr class="mb-8 border-gray-200" />

        <div
            v-if="publications.length"
            class="flex flex-col divide-y divide-gray-100"
        >
            <div
                v-for="pub in publications"
                :key="pub.id"
                class="py-4 flex items-start justify-between gap-6"
            >
                <div class="flex-1">
                    <Button
                        type="button"
                        unstyled
                        @click="goPublication(pub.id)"
                        class="text-left text-gray-900 font-bold hover:underline leading-snug text-xl"
                        :label="pub.title"
                    />
                    <div class="mt-2 text-lg text-gray-600">
                        <span
                            v-for="(author, i) in pub.authors"
                            :key="author.id"
                        >
                            <Button
                                type="button"
                                unstyled
                                @click="goAuthor(author.id)"
                                class="hover:underline hover:text-gray-900 transition"
                                :label="`${author.firstname} ${author.surname}`"
                            /><span v-if="i < pub.authors.length - 1">, </span>
                        </span>
                    </div>
                </div>
                <div class="shrink-0 text-lg text-gray-400 font-medium pt-0.5">
                    <span v-if="pub.firstpage && pub.firstpage !== '0'">
                        Strana: {{ pub.firstpage
                        }}<span v-if="pub.lastpage && pub.lastpage !== '0'"
                            >–{{ pub.lastpage }}</span
                        >
                    </span>
                </div>
            </div>
        </div>

        <p v-else class="text-center text-gray-500 py-10 text-lg">
            Žiadne publikácie v tomto vydaní.
        </p>
    </div>
</template>
