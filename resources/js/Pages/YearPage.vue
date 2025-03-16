<script setup>
import { router } from '@inertiajs/vue3';
import HomeLayout from '@/Layouts/HomeLayout.vue';
import Card from 'primevue/card';

const props = defineProps({
    year: String, // Rok zostáva ako string
    issues: Array, // Unikátne čísla vydaní (number)
    publications: Array // Všetky publikácie za rok
});

// Navigácia do konkrétneho vydania
const goToIssue = (number) => {
    router.get(`/year/${props.year}/${number}`);
};

defineOptions({
  layout: HomeLayout
});
</script>

<template>
  <div class="max-w-4xl mx-auto p-6">
    <h1 class="text-3xl font-bold text-center mb-6">Publikácie za rok {{ year }}</h1>

    <!-- Grid na unikátne čísla vydaní (vydania) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 mb-6">
      <Card 
        v-for="issue in issues" 
        :key="issue.number"
        class="p-4 text-center shadow-md cursor-pointer hover:shadow-lg transition transform hover:scale-105"
        @click="goToIssue(issue.number)"
      >
        <template #title>
          <h2 class="text-lg font-bold">Vydanie {{ issue.number }}</h2>
        </template>
      </Card>
    </div>

    <!-- Zobrazenie publikácií -->
    <div v-if="publications.length">
      <ul class="divide-y divide-gray-300">
        <li v-for="publication in publications" :key="publication.id" class="p-4">
          <h2 class="text-xl font-semibold">{{ publication.title }}</h2>
          <p class="text-gray-600">Vydanie: {{ publication.number }}</p>
        </li>
      </ul>
    </div>
    <p v-else class="text-center text-gray-500">Žiadne publikácie pre tento rok.</p>
  </div>
</template>
