<script setup>
import { ref, watch } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import Card from 'primevue/card'
import Paginator from 'primevue/paginator'
 

// Prijatie rokov ako prop
const props = defineProps({
  years: Object, // Očakávame stránkovaný objekt rokov
  stats: Object,
  
});

// Navigácia na stránku konkrétneho roka
const goToYear = (year) => {
  router.get(`/publications/${year}`);
};

// Zmena stránky pri stránkovaní
const changePage = (event) => {
  router.get(`/?page=${event.page + 1}`);
};



// ✅ Opravený console.log

console.log("YEARS DATA:", props.years);
</script>

<template>
    <div
  class="relative h-[350px] w-full bg-center bg-cover overflow-hidden rounded-2xl"
  style="background-image: url('/images/bc2.png')"
>
 

  <!-- text nad obrázkom -->
  <div class="absolute inset-0 flex items-center">
    <div class="max-w-6xl mx-auto px-6">
      <p class="text-2xl font-semibold text-[rgba(107,176,215,1)]">
        Elektronický archív časopisu
      </p>

      <h1 class="mt-2 text-3xl sm:text-4xl  text-slate-900 font-bold ">
        Obzory matematiky, fyziky a informatiky
      </h1>

      <p class="mt-3 text-xl max-w-xl text-slate-600">
        Časopis publikuje metodické materiály, skúsenosti učiteľov, nové poznatky z teórie vyučovania, ako vedného odboru a nové vedecké poznatky z jednotlivých vedných odborov. 
      </p>

      <!-- ak chceš aj tlačidlá ako v návrhu -->
      <div class="mt-6 flex gap-3">
        <button
          class="px-5 py-3 rounded-xl text-white font-semibold
                 bg-[rgba(107,176,215,1)] hover:bg-[rgba(107,176,215,0.9)] transition"
          @click="router.get('/publications')"
        >
          Prehliadať publikácie
        </button>

        <button
          class="px-5 py-3 rounded-xl font-semibold border border-slate-200 bg-white/80
                 hover:bg-white transition"
          @click="router.get('/about')"
        >
          O časopise
        </button>
      </div>
    </div>
  </div>
</div>


    <!-- SECTION HEADER -->
<div class="mt-10 mx-auto max-w-6xl px-4 text-center">
  <h2 class="text-xl sm:text-2xl font-semibold text-slate-900">
    Ročníky
  </h2>
  <p class="mt-1 text-sm text-slate-600">
    Vyber rok a pokračuj na vydania.
  </p>
</div>


  


<!-- YEARS GRID (modern cards) -->
<div class="mt-8 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 max-w-6xl mx-auto px-4">
  <button
    v-for="yearObj in props.years.data"
    :key="yearObj.year"
    type="button"
    class="group w-full rounded-xl border border-slate-200 bg-white px-6 py-5
       text-left transition-colors
       hover:bg-slate-50
       hover:border-slate-300
       focus:outline-none focus-visible:ring-2 focus-visible:ring-[rgba(107,176,215,0.6)]"

    @click="goToYear(yearObj.year)"
  >
    <!-- horná časť -->
    <div class="flex items-center justify-between">
      <div class="text-3xl font-semibold text-slate-900 tracking-tight">
        {{ yearObj.year }}
      </div>

      <!-- jemná šípka -->
      <svg
        class="h-4 w-4 text-slate-400 group-hover:text-[rgba(107,176,215,1)] transition"
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 20 20"
        fill="currentColor"
      >
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
      </svg>
    </div>

    <!-- spodný popis -->
    <div class="mt-3 text-sm text-slate-600 leading-snug">
      Zobraziť vydania a publikácie z tohto roka.
    </div>
  </button>
</div>


<!-- PAGINATION (nechaj Prime, len obal do moderného kontajnera) -->
<div v-if="props.years.total > 16" class="mt-8 flex justify-center">
  <div class="rounded-2xl border border-slate-200 bg-white px-4 py-3 shadow-sm">
    <Paginator
      :rows="16"
      :totalRecords="props.years.total"
      @page="changePage"
    />
  </div>
</div>

    
<!-- STATS (ucelený panel) -->
<section class="mt-16">
  <div class="rounded-3xl border border-slate-200 bg-white shadow-sm overflow-hidden">
    <!-- horný pásik (jemný gradient ako dizajn) -->
    <div class="px-6 py-4 bg-gradient-to-r from-[rgba(211,227,242,0.8)] via-white to-white">
      <h3 class="text-base font-semibold text-slate-900">Prehľad databázy</h3>
      <p class="mt-1 text-sm text-slate-600">
        Základné štatistiky publikácií, autorov a vydaní.
      </p>
    </div>

    <!-- karty vnútri panelu -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-0">
      <!-- Publikácie -->
      <div class="p-6 border-t sm:border-t-0 sm:border-r border-slate-200">
        <div class="flex items-center gap-3">
          <div class="h-11 w-11 rounded-2xl flex items-center justify-center
                      bg-[rgba(211,227,242,1)] text-[rgba(107,176,215,1)]">
            <!-- icon -->
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path d="M4 3a2 2 0 012-2h6a2 2 0 012 2v14a2 2 0 01-2 2H6a2 2 0 01-2-2V3z" />
            </svg>
          </div>

          <div>
            <div class="text-sm text-slate-500">Počet publikácií</div>
            <div class="text-3xl font-semibold text-slate-900 leading-tight">
              {{ props.stats?.publications ?? '—' }}
            </div>
          </div>
        </div>
      </div>

      <!-- Autori -->
      <div class="p-6 border-t sm:border-t-0 sm:border-r border-slate-200">
        <div class="flex items-center gap-3">
          <div class="h-11 w-11 rounded-2xl flex items-center justify-center
                      bg-[rgba(211,227,242,1)] text-[rgba(107,176,215,1)]">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M10 2a4 4 0 100 8 4 4 0 000-8zM4 16a6 6 0 1112 0v1H4v-1z" clip-rule="evenodd" />
            </svg>
          </div>

          <div>
            <div class="text-sm text-slate-500">Počet autorov</div>
            <div class="text-3xl font-semibold text-slate-900 leading-tight">
              {{ props.stats?.authors ?? '—' }}
            </div>
          </div>
        </div>
      </div>

      <!-- Vydania -->
      <div class="p-6 border-t sm:border-t-0 border-slate-200">
        <div class="flex items-center gap-3">
          <div class="h-11 w-11 rounded-2xl flex items-center justify-center
                      bg-[rgba(211,227,242,1)] text-[rgba(107,176,215,1)]">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" />
              <path d="M7 6h6M7 10h6M7 14h6" stroke="currentColor" stroke-width="1.5" />
            </svg>
          </div>

          <div>
            <div class="text-sm text-slate-500">Počet vydaní</div>
            <div class="text-3xl font-semibold text-slate-900 leading-tight">
              {{ props.stats?.issues ?? '—' }}
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- spodný pásik (malý detail, aby to pôsobilo “hotovo”) -->
    <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 text-sm text-slate-600">
      Tip: Použi vyhľadávanie na rýchle nájdenie autora alebo kľúčového slova.
    </div>
  </div>
</section>

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