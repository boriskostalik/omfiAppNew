<script setup>
import HomeLayout from '@/Layouts/HomeLayout.vue';
import { onMounted } from 'vue';

import Publication from '@/Components/Publication.vue';

const props = defineProps({
    year: String,
    number: String,
    publications: Array
});

// Debugging: Vypíše dáta do konzoly, aby sme videli, čo Vue dostáva
onMounted(() => {
    console.log("Dáta z Laravelu:", props.publications);
});

defineOptions({
  layout: HomeLayout
});
</script>

<template>
    <h1 class="text-3xl font-bold text-center mb-6">
      Publikácie za rok {{ year }} / {{ number }}
    </h1>
    <a :href="`https://www.omfi.ukf.sk/documents/OMFI_${year}_${number}`" class="text-blue-500 hover:underline mb-4 block text-center">
      OMFI {{ year }}/{{ number }} (pdf)
    </a>

    <div v-if="publications.length">
        <div v-for="publication in publications" :key="publication.id" class="mb-4">
          <Publication :publication="publication" withAuthors/>
        </div>
    </div>

    <p v-else class="text-center text-gray-500">Žiadne publikácie v tomto vydaní.</p>
</template>
