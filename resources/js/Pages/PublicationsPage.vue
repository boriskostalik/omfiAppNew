<template>
  <div class="w-full max-w-[1000px] mx-auto px-4 mt-16">
    <Header title="Publikácie" hasSearch @search="query => submitSearch(query)" />

    <PublicationFilter
      :options="props.options"
      v-model:year="selectedYear"
      v-model:number="selectedNumber"
      v-model:institute="selectedInstitute"

      v-model:authorId="selectedAuthorId"
      @apply="applyFilters"
      @clear="clearFilters"
    />

    <div class="w-full mt-16">
      <div class="md:hidden mb-2">
        <PublicationSortBar v-model:sortKey="sortKey" @change="applyFilters" />
      </div>

      <div class="relative w-full ">
        <div class="flex justify-center">
          <Paginator
  :rows="perPage"
  :totalRecords="publications.total"
  :rowsPerPageOptions="[10, 20, 30]"
  :first="(currentPage - 1) * perPage"
  :pageLinkSize="3"
  @page="changePage"
  
/>

        </div>

        <div class="hidden md:block absolute right-0 top-1/2 -translate-y-1/2 w-48">
          <PublicationSortBar v-model:sortKey="sortKey" @change="applyFilters" />
        </div>
      </div>
    </div>
    <div class="mt-6">
      <div v-for="publication in publications.data" :key="publication.id" class="mb-4">
        <Publication :publication="publication" />
      </div>
    </div>
    <div class="flex justify-center w-full mt-3">
      <Paginator
        :rows="perPage"
        :totalRecords="publications.total"
        :rowsPerPageOptions="[10, 20, 30]"
        :first="(currentPage - 1) * perPage"
        :pageLinkSize="3"
        @page="changePage"
      />
    </div>
    
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import Paginator from 'primevue/paginator'
import { Select } from 'primevue'
import Header from '@/Components/Header.vue'
import Publication from '@/Components/Publication.vue'
import PublicationFilter from '@/Components/PublicationFilter.vue'
import PublicationSortBar from '@/Components/PublicationSortBar.vue'

const props = defineProps({
  publications: Object,
  per_page: [Number, String],
  search: String,
  filters: Object,
  options: Object,
})

const currentPage = ref(props.publications.current_page || 1)
const perPage = ref(Number(props.publications.per_page) || 10)

const selectedInstitute = ref(props.filters?.institute || null)

const selectedAuthorId = ref(props.filters?.author_id || null)
const selectedYear = ref(props.filters?.year || null)
const selectedNumber = ref(props.filters?.number || null)

const searchValue = ref(props.search || '')
const sortKey = ref(props.filters?.sortKey || 'title_asc')

const applyFilters = ({ page = 1, per_page = perPage.value } = {}) => {
  if (page === 1) currentPage.value = 1

  router.get(
    route('publications.index'),
    {
      page,
      per_page,
      search: searchValue.value || undefined,
      year: selectedYear.value || undefined,
      number: selectedNumber.value || undefined,
      institute: selectedInstitute.value || undefined,

      author_id: selectedAuthorId.value || undefined,
      sortKey: sortKey.value || undefined,
    },
    {
      preserveState: true,
      preserveScroll: true,
      replace: true,
    }
  )
}

const changePage = (val) => {
  currentPage.value = val.page + 1
  perPage.value = val.rows
  applyFilters({ page: currentPage.value, per_page: perPage.value })
}

const submitSearch = (query) => {
  searchValue.value = query
  applyFilters({ page: 1 })
}

const clearFilters = () => {
  selectedYear.value = null
  selectedNumber.value = null
  selectedInstitute.value = null

  selectedAuthorId.value = null
  searchValue.value = ''
  applyFilters({ page: 1 })
}
</script>
