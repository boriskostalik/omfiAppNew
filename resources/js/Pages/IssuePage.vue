<script setup>
import HomeLayout from '@/Layouts/HomeLayout.vue';
import { onMounted } from 'vue';

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
  <div class="max-w-4xl mx-auto p-6">
    <h1 class="text-3xl font-bold text-center mb-6">
      Publikácie za rok {{ year }} – Vydanie {{ number }}
    </h1>

    <div v-if="publications.length">
      <ul class="divide-y divide-gray-300">
        <li v-for="publication in publications" :key="publication.id" class="p-4">
          <h2 class="text-xl font-semibold">{{ publication.title }}</h2>
          
          <!-- Zobrazenie autorov -->
          <p v-if="publication.authors.length" class="text-gray-700">
            <strong>Autori:</strong>
            <span v-for="(author, index) in publication.authors" :key="author.id">
              {{ author.name }} 
              <span v-if="author.is_editor === 'Y'">(Editor)</span>
              <span v-if="index < publication.authors.length - 1">, </span>
            </span>
          </p>
          
          <p v-else class="text-gray-500 italic">Autor neznámy</p>
        </li>
      </ul>
    </div>

    <p v-else class="text-center text-gray-500">Žiadne publikácie v tomto vydaní.</p>
  </div>
</template>
