<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import Paginator from 'primevue/paginator'

const props = defineProps({
  years: Object,
  stats: Object,
})

const rows = 16
const currentPage = ref(props.years?.current_page || 1)

const goToYear = (year) => {
  router.get(`/publications/${year}`)
}

const changePage = (event) => {
  currentPage.value = event.page + 1
  router.get('/', { page: currentPage.value }, { preserveState: true, preserveScroll: true, replace: true })
}
</script>

<template>
  <div
    class="relative h-[350px] w-full bg-center bg-cover overflow-hidden rounded-2xl"
    style="background-image: url('/images/bc2.png')"
  >
    <div class="absolute inset-0 flex items-center">
      <div class="max-w-6xl mx-auto xl:pr-80">
        <p class="text-2xl font-semibold text-[rgba(107,176,215,1)]">
          Elektronický archív časopisu
        </p>

        <h1 class="mt-2 text-3xl sm:text-4xl text-slate-900 font-bold">
          Obzory matematiky, fyziky a informatiky
        </h1>

        <p class="mt-3 text-xl max-w-xl text-slate-600">
          Časopis publikuje metodické materiály, skúsenosti učiteľov, nové poznatky z teórie vyučovania, ako vedného odboru a nové vedecké poznatky z jednotlivých vedných odborov.
        </p>

        <div class="mt-6 flex gap-3">
          <button
            class="px-5 py-3 rounded-xl text-white font-semibold bg-[rgba(107,176,215,1)] hover:bg-[rgba(107,176,215,0.9)] transition"
            @click="router.get('/publications')"
          >
            Prehliadať publikácie
          </button>

          <button
            class="px-5 py-3 rounded-xl font-semibold border border-slate-200 bg-white/80 hover:bg-white transition"
            @click="router.get('/about')"
          >
            O časopise
          </button>
        </div>
      </div>
    </div>
  </div>

  <div class="mt-10 mx-auto max-w-6xl px-4 text-center">
    <h2 class="text-xl sm:text-2xl font-semibold text-slate-900">Ročníky</h2>
    <p class="mt-1 text-sm text-slate-600">Vyber rok a pokračuj na vydania.</p>
  </div>

  <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 max-w-6xl mx-auto px-4">
    <button
      v-for="yearObj in props.years.data"
      :key="yearObj.year"
      type="button"
      class="group w-full rounded-xl border border-slate-200 bg-white px-6 py-5 text-left transition-colors hover:bg-slate-50 hover:border-slate-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-[rgba(107,176,215,0.6)]"
      @click="goToYear(yearObj.year)"
    >
      <div class="flex items-center justify-between">
        <div class="text-3xl font-semibold text-slate-900 tracking-tight">
          {{ yearObj.year }}
        </div>

        <svg
          class="h-4 w-4 text-slate-400 group-hover:text-[rgba(107,176,215,1)] transition"
          xmlns="http://www.w3.org/2000/svg"
          viewBox="0 0 20 20"
          fill="currentColor"
        >
          <path
            fill-rule="evenodd"
            d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
            clip-rule="evenodd"
          />
        </svg>
      </div>

      <div class="mt-3 text-sm text-slate-600 leading-snug">
        Zobraziť vydania a publikácie z tohto roka.
      </div>
    </button>
  </div>

  <div v-if="props.years.total > rows" class="mt-8 flex justify-center px-4">
    
      <Paginator
        :rows="rows"
        :totalRecords="props.years.total"
        :first="(currentPage - 1) * rows"
        :pageLinkSize="3"
        template="PageLinks"
        @page="changePage"
      />
   

  </div>

  <section class="mt-16">
    <div class="rounded-3xl border border-slate-200 bg-white shadow-sm overflow-hidden">
      <div class="px-6 py-5 bg-gradient-to-r from-[rgba(211,227,242,0.8)] via-white to-white text-center">
        <h3 class="text-base font-semibold text-slate-900">Prehľad databázy</h3>
        <p class="mt-2 text-sm text-slate-600 max-w-xl mx-auto">
          Základné štatistiky publikácií, autorov a vydaní.
        </p>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-3 gap-0 max-w-6xl mx-auto px-4">
        <div class="p-6 border-t sm:border-t-0 sm:border-r border-slate-200">
          <div class="flex items-center gap-3">
            <div class="h-11 w-11 rounded-2xl flex items-center justify-center bg-[rgba(211,227,242,1)] text-[rgba(107,176,215,1)]">
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

        <div class="p-6 border-t sm:border-t-0 sm:border-r border-slate-200">
          <div class="flex items-center gap-3">
            <div class="h-11 w-11 rounded-2xl flex items-center justify-center bg-[rgba(211,227,242,1)] text-[rgba(107,176,215,1)]">
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

        <div class="p-6 border-t sm:border-t-0 border-slate-200">
          <div class="flex items-center gap-3">
            <div class="h-11 w-11 rounded-2xl flex items-center justify-center bg-[rgba(211,227,242,1)] text-[rgba(107,176,215,1)]">
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

      <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 text-sm text-slate-600">
        Tip: Použi vyhľadávanie na rýchle nájdenie autora alebo kľúčového slova.
      </div>
    </div>
  </section>
</template>
