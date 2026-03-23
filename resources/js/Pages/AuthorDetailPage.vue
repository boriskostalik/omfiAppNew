<script setup>
import { router } from "@inertiajs/vue3";
import HomeLayout from "@/Layouts/HomeLayout.vue";

const props = defineProps({
    author: { type: Object, required: true },
    publicationsByYear: { type: Array, default: () => [] },
});

defineOptions({ layout: HomeLayout });

const goPublication = (id) => router.get(route("publications.detail", id));
</script>

<template>
    <div class="max-w-4xl mx-auto px-4 py-14">
        <div class="mb-16">
            <h1 class="text-5xl font-bold text-gray-900 leading-snug mb-4">
                {{
                    author.cleanname?.replace(",", "") ??
                    `${author.firstname} ${author.surname}`
                }}
            </h1>
            <div class="flex flex-col gap-2 text-gray-500 text-lg">
                <span v-if="author.institute"
                    >Inštitút: {{ author.institute }}</span
                >
                <span v-if="author.email">
                    E-mail:
                    <a
                        :href="`mailto:${author.email}`"
                        class="hover:underline hover:text-gray-900 transition"
                        >{{ author.email }}</a
                    >
                </span>
                <span v-if="author.url">
                    <a
                        :href="author.url"
                        target="_blank"
                        class="hover:underline hover:text-gray-900 transition"
                    >
                        {{ author.url }}
                    </a>
                </span>
            </div>
        </div>
        <div v-if="publicationsByYear?.length">
            <h2
                class="text-2xl font-bold text-gray-900 mb-5 uppercase tracking-wide"
            >
                Zoznam publikácií
            </h2>
            <div
                v-for="group in publicationsByYear"
                :key="group.year"
                class="mb-10 pl-6"
            >
                <h2
                    class="text-2xl font-bold text-gray-900 mb-5 uppercase tracking-wide"
                >
                    {{ group.year }}
                </h2>
                <hr class="mb-8 border-gray-200" />
                <div class="flex flex-col divide-y divide-gray-100 pl-6">
                    <div
                        v-for="pub in group.publications"
                        :key="pub.id"
                        class="py-4 flex items-start justify-between gap-6"
                    >
                        <div class="flex-1">
                            <button
                                type="button"
                                @click="goPublication(pub.id)"
                                class="text-left text-gray-900 font-bold hover:underline leading-snug text-xl"
                            >
                                {{ pub.title }}
                            </button>
                            <div class="mt-2 text-lg text-gray-600">
                                <span v-for="(a, i) in pub.authors" :key="a.id">
                                    {{ a.firstname }} {{ a.surname
                                    }}<span v-if="i < pub.authors.length - 1"
                                        >,
                                    </span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <p v-else class="text-center text-gray-500 py-10 text-lg">
            Žiadne publikácie od tohto autora.
        </p>
    </div>
</template>
