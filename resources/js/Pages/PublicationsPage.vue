<template>
  <div class="w-full max-w-[1000px] mx-auto px-4 mt-16">
    <Header title="Zoznam publikácií" hasSearch @search="(query) => submitSearch(query)" />
    <Card class="mt-6">
      <template #content>
        <PublicationFilter
          :options="props.options"
          v-model:year="selectedYear"
          v-model:number="selectedNumber"
          v-model:institute="selectedInstitute"
          v-model:authorId="selectedAuthorId"
          @apply="applyFilters"
          @clear="clearFilters"
        />
      </template>
    </Card>
    <div v-if="!publications.data?.length" class="mt-8">
      <Message severity="info" :closable="false">
        Zadaným kritériám nevyhovuje žiadna publikácia.
      </Message>
    </div>
    <div v-else class="mt-8 mb-4">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div class="text-sm text-slate-600 text-center md:text-left">
          Nájdených výsledkov:
          <span class="font-semibold text-slate-900 inline-block tabular-nums min-w-[4ch] text-right">
            {{ publications.total }}
          </span>
        </div>
        <div class="flex justify-center md:flex-1">
          <Paginator
            :rows="perPage"
            :totalRecords="publications.total"
            :rowsPerPageOptions="[10, 20, 30]"
            :first="(currentPage - 1) * perPage"
            :pageLinkSize="3"
            @page="changePage"
          />
        </div>
        <div class="md:w-48 mx-auto md:mx-0 md:ml-auto">
          <PublicationSortBar v-model:sortKey="sortKey" @change="applyFilters" />
        </div>
      </div>
    </div>
    <div v-if="publications.data?.length" class="mt-6">
      <div v-for="publication in publications.data" :key="publication.id" class="mb-4">
        <Publication :publication="publication" />
      </div>
    </div>
    <div
      v-if="publications.data?.length && publications.total > perPage"
      class="flex justify-center w-full mt-6"
    >
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
import Card from 'primevue/card'
import Message from 'primevue/message'
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

const currentPage = ref(props.publications?.current_page || 1)
const perPage = ref(Number(props.publications?.per_page) || 10)
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
