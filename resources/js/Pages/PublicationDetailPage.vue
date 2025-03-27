<template>
    <Header title="Publication Detail" />
    <h2 class="text-xl font-semibold">{{ publication.title }}</h2>

    <div class="mt-2">
        <p class="text-gray-700">
            <strong>English title:</strong> {{ publication.title_eng }}
        </p>
        <p class="text-gray-700">
            <strong>Journal:</strong> {{ publication.journal }}
        </p>
        <p class="text-gray-700">
            <strong>Year:</strong> {{ publication.year }}
        </p>
        <p class="text-gray-700">
            <strong>Volume:</strong> {{ publication.volume }}
        </p>
        <p class="text-gray-700">
            <strong>Pages:</strong> {{ publication.firstpage }} - {{ publication.lastpage }}
        </p>
        <p class="text-gray-700">
            <strong>Bibtex Cite ID:</strong> {{ publication.bibtex_id }}
        </p>
        <p @click="isBibtexVisible = true" class="text-blue-600 hover:underline cursor-pointer">
            <strong>Bibtex</strong>
        </p>
        <p @click="isRISVisible = true" class="text-blue-600 hover:underline cursor-pointer">
            <strong>RIS</strong>
        </p>
        <p v-if="publication.doi" class="text-gray-700">
            <strong>DOI:</strong> 
            <a :href="'https://doi.org/' + publication.doi" class="text-blue-600 hover:underline">{{ publication.doi }}</a>
        </p>

        <div class="mt-4">
            <h2 class="text-lg font-semibold">Authors</h2>
            <ul v-if="publication.authors.length" class="list-disc ml-6">
                <div v-for="author in publication.authors" :key="author.id">
                    <Author :author="author" />
                </div>
            </ul>
            <p v-else class="text-gray-500 italic">No authors available</p>
        </div>
        <div class="mt-4">
            <h2 class="text-lg font-semibold">Abstract</h2>
            <p>{{ publication.note }}</p>
        </div>
    </div>
    <!-- TODO add BIBTEX and RIS -->
     <Bibtex :publication="publication" :visible="isBibtexVisible" @close="isBibtexVisible = false" />
     <RIS :publication="publication" :visible="isRISVisible" @close="isRISVisible = false" />
</template>

<script setup>
import Author from '@/Components/Author.vue';
import Header from '@/Components/Header.vue';
import HomeLayout from '@/Layouts/HomeLayout.vue';

import { defineProps, defineOptions, ref } from 'vue';
import Bibtex from '@/Components/Bibtex.vue';   
import RIS from '@/Components/RIS.vue';
const props = defineProps({
    publication: Object,
});
defineOptions({
    layout: HomeLayout,
})

const isBibtexVisible = ref(false);
const isRISVisible = ref(false);
</script>