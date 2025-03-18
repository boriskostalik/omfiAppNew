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
  <div class="max-w-6xl mx-auto p-6">
    <h1 class="text-3xl font-bold text-center mb-6">
      Publikácie za rok {{ year }} / {{ number }}
    </h1>

    <div v-if="publications.length">
      <ul class="divide-y divide-gray-300">
        <li v-for="publication in publications" :key="publication.id" class="p-4">
          <Publication :publication="publication" />
          
        </li>
      </ul>
    </div>

    <p v-else class="text-center text-gray-500">Žiadne publikácie v tomto vydaní.</p>
  </div>
</template>
