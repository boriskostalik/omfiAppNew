<script setup>
import { ref, watch } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import Card from 'primevue/card'
import Paginator from 'primevue/paginator'
import HomeLayout from '@/Layouts/HomeLayout.vue'

// Prijatie rokov ako prop
const props = defineProps({
  years: Object, // Očakávame stránkovaný objekt rokov
});

// Navigácia na stránku konkrétneho roka
const goToYear = (year) => {
  router.get(`/publications/${year}`);
};

// Zmena stránky pri stránkovaní
const changePage = (event) => {
  router.get(`/?page=${event.page + 1}`);
};

// Nastavenie layoutu
defineOptions({
  layout: HomeLayout
});

// ✅ Opravený console.log

console.log("YEARS DATA:", props.years);
</script>

<template>
    <!-- Nadpis -->
    <h1 class="text-3xl font-bold text-center mb-8 text-gray-800">
      Dostupné vydania podľa rokov
    </h1>

    <!-- Grid pre roky -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
      <Card 
        v-for="yearObj in props.years.data" 
        :key="yearObj.year" 
        class="custom-card p-6 text-center cursor-pointer transition-all hover:shadow-xl"
        @click="goToYear(yearObj.year)"
      >
        <template #title>
          <h2 class="text-2xl font-semibold text-gray-900">
            {{ yearObj.year }}
          </h2>
        </template>
      </Card>
    </div>

    <!-- Paginácia -->
    <div v-if="props.years.total > 16" class="pagination-container mt-8">
      <Paginator 
        :rows="16" 
        :totalRecords="props.years.total" 
        @page="changePage"
      />
    </div>

    <p class="text-gray-600 text-center mt-4">Počet záznamov na tejto stránke: {{ props.years.data.length }}</p>
    <p class="text-gray-600 text-center">Celkový počet záznamov: {{ props.years.total }}</p>

</template>



<style>
/* Minimalistický štýl kariet so zvýrazneným efektom */
.custom-card {
  background: white;
  border-radius: 10px;
  border: 1px solid #e5e7eb;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.custom-card:hover {
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15); /* Výraznejší tieň pri hoveri */
  transform: translateY(-4px); /* Mierne zdvihnutie dlaždice */
}

/* Minimalistická pagination */
.pagination-container {
  display: flex;
  justify-content: center;
}

.pagination-container .paginator {
  display: flex;
  gap: 6px;
}

.pagination-container .paginator button {
  background: white;
  color: #4a5568;
  padding: 8px 12px;
  border-radius: 6px;
  border: 1px solid #e5e7eb;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease-in-out;
}

.pagination-container .paginator button:hover {
  background: #f3f4f6;
}

.pagination-container .paginator button:disabled {
  background: #e5e7eb;
  color: #9ca3af;
  cursor: not-allowed;
}
</style>