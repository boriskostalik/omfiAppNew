<template>
  <div>
    <div class="max-w-6xl mx-auto p-6">
      <Header title="Publikácie" has-search @search="query => submitSearch(query)"/>
    </div>
    <DataTable
      v-model:filters="filters"
      :value="publications.data"
      :sortField="sortField"
      :sortOrder="sortOrder"
      dataKey="id"
      tableStyle="min-width: 60rem"
      editMode="row"
      @sort="onSort"
      :pt="{
        table: { style: 'min-width: 50rem' },
        column: {
            bodycell: ({ state }) => ({
                style:  state['d_editing']&&'padding-top: 0.75rem; padding-bottom: 0.75rem'
            })
        }
    }"
    >

    <!-- Title -->
    <Column field="title" header="Title" sortable style="width: 30%">
      <template #body="{ data }">
        {{ data.title }}
      </template>
    </Column>

    <!-- Type -->
    <Column field="type" header="Type" sortable style="width: 15%">
      <template #body="{ data }">
        <Tag :value="data.type" />
      </template>
    </Column>

    <!-- Year -->
    <Column field="year" header="Year" sortable style="width: 10%">
      <template #body="{ data }">
        {{ data.year }}
      </template>
    </Column>

    <!-- Journal -->
    <Column field="journal" header="Journal"  style="width: 20%">
      <template #body="{ data }">
        {{ data.journal }}
      </template>
    </Column>

    <!-- Volume & Number -->
    <Column header="Number" style="width: 15%">
      <template #body="{ data }">
        {{ data.year }} / {{ data.number }}
      </template>
    </Column>

    <!-- DOI -->
    <Column field="doi" header="DOI" style="width: 20%">
      <template #body="{ data }">
        <a :href="data.doi" target="_blank" class="text-blue-500 hover:underline">{{ data.doi }}</a>
      </template>
    </Column>

    <!-- Authors -->
    <Column header="Authors" style="width: 25%">
      <template #body="{ data }">
        <span v-if="data.authors.length">{{ data.authors.map(a => `${a.cleanname}`).join(', ') }}</span>
        <span v-else>No authors</span>
      </template>
    </Column>
    <Column class="w-24 !text-end">
      <template #header>
        <Button @click="isModalVisible = true">Pridať</Button>
      </template>
        <template #body="{ data }">
          <div class="flex gap-2">
            <Button icon="pi pi-pencil" @click="onRowEdit(data.id)" severity="secondary" rounded></Button>
            <Button icon="pi pi-trash" @click="onRowDelete(data.id)" severity="secondary" rounded></Button>
          </div>
        </template>
    </Column>

    <!-- Empty State -->
    <template #empty>
      No publications found.
    </template>

  </DataTable>
  <Paginator
    v-if="publications.next_page_url || publications.prev_page_url"
    :rows="perPage"
    :totalRecords="publications.total"
    :rowsPerPageOptions="[10, 20, 30]"
    :first="(currentPage - 1) * perPage"
    @page="changePage"
  />
    {{ sortOrder, sortField }}
  </div>
  <PublicationForm 
  :publication="publicationToEdit" 
  :visible="isModalVisible" 
  :entered_by="entered_by" 
  :authors="authors"
  @close="closeModal()"
  />
</template>

<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { Button, DataTable, Column, Tag, Paginator } from 'primevue';
import Header from '@/Components/Header.vue';
import PublicationForm from '@/Pages/Dashboard/PublicationForm.vue';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
defineOptions({
    layout: AuthenticatedLayout,
});

const props = defineProps({
  publications: Object,
  entered_by: Number,
  authors: Array,
  filters: Object, // Add filters from backend
  sortField: String,
  sortOrder: String,
});
console.log(props)

const filters = ref({
  title: { value: props.filters?.title || null },
  type: { value: props.filters?.type || null },
  year: { value: props.filters?.year || null },
});

const currentPage = ref(props.publications.current_page || 1);
const perPage = ref(Number(props.publications.per_page) || 10);
const sortField = ref(props.sortField || 'title');
const sortOrder = ref(props.sortOrder === 'desc' ? -1 : 1);

const isModalVisible = ref(false);
const publicationToEdit = ref(null);

const closeModal = () => {
  isModalVisible.value = false;
  publicationToEdit.value = null;
};

const changePage = (val) => {
  currentPage.value = val.page + 1;
  perPage.value = val.rows;

  router.get(route('publications.dashboard'), {
    page: currentPage.value,
    per_page: perPage.value,
  });
};

const cleanParams = (params) => {
  return Object.fromEntries(
    Object.entries(params).filter(([_, value]) => value != null && value !== '')
  );
};

const submitSearch = (query) => {
  router.get(route('publications.dashboard'), {
    page: 1,
    search: query,
  });
};

const onRowDelete = (id) => {
    if (confirm('Are you sure?')) {
        router.delete(`/dashboard/publications/${id}`);
    }
};

const syncParams = () => {
  return cleanParams({
    page: currentPage.value,
    per_page: perPage.value,
    sortField: sortField.value,
    sortOrder: sortOrder.value === 1 ? 'asc' : 'desc',
    title: filters.value.title?.value,
    type: filters.value.type?.value,
    year: filters.value.year?.value,
    journal: filters.value.journal?.value,
  });
};


// Handle sorting and preserve pagination and filters
const onSort = (event) => {
  sortField.value = event.sortField;
  sortOrder.value = event.sortOrder;

  router.get(route('publications.dashboard'), syncParams());
};

const onRowEdit = (id) => {
    isModalVisible.value = true;
    publicationToEdit.value = props.publications.data.find((p) => p.id === id);
};
</script>

<style scoped>
input {
display: block;
}
</style>