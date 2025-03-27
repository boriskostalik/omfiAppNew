<template>
    <div>
        <div class="max-w-6xl mx-auto p-6">
            <Header title="Authors" has-search @search="query => submitSearch(query)" />
        </div>
      <DataTable
        v-model:filters="filters"
        :value="authors.data"
        :sortField="sortField"
        :sortOrder="sortOrder"
        filterDisplay="menu"
        selectionMode="single"
        dataKey="id"
        tableStyle="min-width: 60rem"
        @sort="onSort"
      >
        <!-- First Name -->
        <Column field="firstname" header="First Name" sortable style="width: 25%">
          <template #body="{ data }">
            {{ data.firstname }}
          </template>
        </Column>
  
        <!-- Last Name -->
        <Column field="surname" header="Last Name" sortable style="width: 25%">
          <template #body="{ data }">
            {{ data.surname }}
          </template>
        </Column>
  
        <!-- Publications -->
        <Column header="Publications" style="width: 35%">
          <template #body="{ data }">
            <span v-if="data.authors?.length">
              {{ data.publications.map(p => p.title).join(', ') }}
            </span>
            <span v-else>No publications</span>
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
          No authors found.
        </template>
  
      </DataTable>
      <Paginator
        v-if="authors.next_page_url || authors.prev_page_url"
        :rows="perPage"
        :totalRecords="authors.total"
        :rowsPerPageOptions="[10, 20, 30]"
        :first="(currentPage - 1) * perPage"
        @page="changePage"
      />
    </div>
    <PublicationForm 
      :author="authorToEdit" 
      :visible="isModalVisible"
      @close="closeModal()"
    />
  </template>
  
<script setup>
import { reactive, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { DataTable, Column, Paginator, Button } from 'primevue';
import Header from '@/Components/Header.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PublicationForm from '@/Pages/Dashboard/AuthorForm.vue';

defineOptions({
    layout: AuthenticatedLayout,
});

const props = defineProps({
    authors: Object,
    filters: Object,
    sortField: String,
    sortOrder: String,
});

const filters = ref({
    firstname: { value: props.filters?.firstname || null },
    lastname: { value: props.filters?.lastname || null },
    year: { value: props.filters?.year || null },
});

const currentPage = ref(props.authors.current_page || 1);
const perPage = ref(Number(props.authors.per_page) || 10);
const sortField = ref(props.sortField || 'firstname');
const sortOrder = ref(props.sortOrder === 'desc' ? -1 : 1);


const isModalVisible = ref(false);
const authorToEdit = ref(null);

const cleanParams = (params) => {
    return Object.fromEntries(
        Object.entries(params).filter(([_, value]) => value != null && value !== '')
    );
};
const changePage = (val) => {
    currentPage.value = val.page + 1;
    perPage.value = val.rows;
    router.get(route('authors.dashboard'), {
        page: currentPage.value,
        per_page: perPage.value,
    });
};

const syncParams = () => {
    return cleanParams({
        page: currentPage.value,
        per_page: perPage.value,
        sortField: sortField.value,
        sortOrder: sortOrder.value === 1 ? 'asc' : 'desc',
        firstname: filters.value.firstname?.value,
        lastname: filters.value.lastname?.value,
    });
};

const submitSearch = (query) => {
    router.get(route('authors.dashboard'), {
        page: 1,
        search: query,
    });
};

const onSort = (event) => {
    sortField.value = event.sortField;
    sortOrder.value = event.sortOrder;
    router.get(route('authors.dashboard'), syncParams());
};

const closeModal = () => {
  isModalVisible.value = false;
  authorToEdit.value = null;
};

const onRowEdit = (id) => {
    isModalVisible.value = true;
    authorToEdit.value = props.authors.data.find((p) => p.id === id);
};

</script>

<style scoped>
input {
display: block;
}
</style>
  