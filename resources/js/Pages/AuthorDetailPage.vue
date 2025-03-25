<template>
<Header title="Author Detail" />
<h2 class="text-xl font-semibold">{{ author.firstname }} {{ author.surname }}</h2>
<div class="mt-2">
    <p class="text-gray-700">
        <strong>Email:</strong> 
        <a :href="'mailto:' + author.email" class="text-blue-600 hover:underline">{{ author.email }}</a>
    </p>

    <p v-if="author.institute" class="text-gray-700">
        <strong>Institute:</strong> {{ author.institute }}
    </p>
    <p v-else class="text-gray-500 italic">No institute specified</p>

    <p v-if="author.url" class="text-gray-700">
        <strong>Website:</strong> 
        <a :href="author.url" target="_blank" class="text-blue-600 hover:underline">{{ author.url }}</a>
    </p>
    <p v-else class="text-gray-500 italic">No website available</p>
    <!-- TODO: pridat nenajdene publikacie -->
    <div class="mt-4">
        <h2 class="text-lg font-semibold">Publikácie</h2>
        <div v-if="!publicationsByYear.length">Nenašli sa žiadne publikácie od autora</div>   
        <div v-for="group in publicationsByYear" :key="group.year">
        <h2>{{ group.year }}</h2>
        <Publication 
            v-for="publication in group.publications"
            :key="publication.id" 
            :publication="publication" 
        />
        <hr>
    </div>
</div>
</div>
</template>
<script setup>
import HomeLayout from '@/Layouts/HomeLayout.vue';
import Header from '@/Components/Header.vue';
import Publication from '@/Components/Publication.vue';
const props = defineProps({
    author: Object,
    publicationsByYear: Object,
});
defineOptions({
    layout: HomeLayout,
});
</script>