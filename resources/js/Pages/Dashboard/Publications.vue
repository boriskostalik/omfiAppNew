<template>
    <div>
      <h1 class="text-2xl font-bold mb-4">New Publication</h1>
  
      <!-- <form @submit.prevent="submit">
        <div class="grid md:grid-cols-2 sm:grid-cols-1 gap-4">
          <Dropdown v-model="form.type" :options="types" placeholder="Type of publication" class="w-full" />
  
          <InputText v-model="form.title" placeholder="Title" class="p-2" required />
          <InputText v-model="form.title_eng" placeholder="English Title" class="p-2" />
  
          <InputText v-model="form.mesc" placeholder="MESC" class="p-2" />
          <InputText v-model="form.bibtex_id" placeholder="BibTeX ID" class="p-2" />
  
          <InputText v-model="form.year" placeholder="Year" class="p-2" required />
          <InputText v-model="form.actualyear" placeholder="Actual Year" class="p-2" />
  
          <InputText v-model="form.journal" placeholder="Journal" class="p-2" />
          <InputText v-model="form.volume" placeholder="Volume" class="p-2" />
  
          <InputText v-model="form.number" placeholder="Number" class="p-2" />
          <Dropdown v-model="form.month" :options="months" placeholder="Month" class="w-full" />
  
          <InputText v-model="form.firstpage" placeholder="First Page" class="p-2" />
          <InputText v-model="form.lastpage" placeholder="Last Page" class="p-2" />
  
          <InputText v-model="form.issn" placeholder="ISSN" class="p-2" />
          <InputText v-model="form.isbn" placeholder="ISBN" class="p-2" />
  
          <InputText v-model="form.url" placeholder="URL" class="p-2" />
          <InputText v-model="form.doi" placeholder="DOI" class="p-2" />
  
          <InputText v-model="form.crossref" placeholder="Crossref" class="p-2" />
          <InputText v-model="form.namekey" placeholder="Key" class="p-2" />
  
          <InputText v-model="form.keywords" placeholder="Keywords" class="p-2" />
          <Textarea v-model="form.abstract" placeholder="Abstract" rows="5" class="p-2" />
        </div>
  
        <Button type="submit" label="Submit" class="mt-4" />
      </form> -->

    <DataTable
      v-model:filters="filters"
      :value="publications.data"
      :sortField="sortField"
      :sortOrder="sortOrder"
      filterDisplay="menu"
      selectionMode="single"
      dataKey="id"
      tableStyle="min-width: 60rem"
      @sort="onSort"
    >

    <!-- Header with Global Search -->
    <template #header>
      <IconField>
        <InputIcon>
          <i class="pi pi-search" />
        </InputIcon>
        <InputText v-model="filters['global'].value" placeholder="Global Search" @input="onFilter" />
      </IconField>
    </template>

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
    <Column field="journal" header="Journal" sortable style="width: 20%">
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
        <span v-if="data.authors.length">{{ data.authors.map(a => `${a.firstname} ${a.lastname}`).join(', ') }}</span>
        <span v-else>No authors</span>
      </template>
    </Column>

    <!-- Empty State -->
    <template #empty>
      No publications found.
    </template>

  </DataTable>
  <Paginator
        :rows="perPage"
        :totalRecords="publications.total"
        :rowsPerPageOptions="[10, 20, 30]"
        :first="(currentPage - 1) * perPage"
        @page="changePage"
      />
      {{ sortOrder, sortField }}
    </div>
  </template>

<script setup>
import { reactive, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { InputText, Textarea, Dropdown, Button, DataTable, Column, Tag, IconField, InputIcon, Paginator } from 'primevue';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
defineOptions({
    layout: AuthenticatedLayout,
});

const props = defineProps({
  publications: Object,
  user: Object,
  filters: Object, // Add filters from backend
  sortField: String,
  sortOrder: String,
});

const filters = ref({
  title: { value: props.filters?.title || null },
  type: { value: props.filters?.type || null },
  year: { value: props.filters?.year || null },
  journal: { value: props.filters?.journal || null },
  global: { value: null },
});

const currentPage = ref(props.publications.current_page || 1);
const perPage = ref(Number(props.publications.per_page) || 10);
const sortField = ref(props.sortField || 'title');
const sortOrder = ref(props.sortOrder === 'desc' ? -1 : 1);

const changePage = (val) => {
  currentPage.value = val.page + 1;
  perPage.value = val.rows;

  router.get(route('publications.dashboard'), {
    page: currentPage.value,
    per_page: perPage.value,
  });
};


const onFilter = (val) => {
    console.log(val);
  router.get(route('publications.dashboard'), {
    // search: filters.value.global?.value,
    title: filters.value.title?.value,
    type: filters.value.type?.value,
    year: filters.value.year?.value,
    journal: filters.value.journal?.value
  });

};

const onSort = (event) => {
  sortField.value = sortField.value;
  sortOrder.value =  sortOrder.value;
  
  router.get(route('publications.dashboard'), {
    page: currentPage.value,
    per_page: perPage.value,
    sortField: event.sortField,
    sortOrder: event.sortOrder === 1 ? 'asc' : 'desc',
    title: filters.value.title?.value,
    type: filters.value.type?.value,
    year: filters.value.year?.value,
    journal: filters.value.journal?.value,
  });
  console.log(filters.value);
};

const onGlobalSearch = () => {
  onFilter();
};
    const form = reactive({
    type: '',
    title: '',
    title_eng: '',
    mesc: '',
    bibtex_id: '',
    year: '',
    actualyear: '',
    journal: 'Obzory matematiky, fyziky a informatiky',
    volume: '',
    number: '',
    month: '',
    firstpage: '',
    lastpage: '',
    issn: '',
    isbn: '',
    url: '',
    doi: '',
    crossref: '',
    namekey: '',
    keywords: '',
    abstract: '',
    entered_by: props.user.id.toString(),
    });

    const types = [
        'Article', 'Book', 'Booklet', 'Inbook', 'Incollection', 'Inproceedings', 'Manual',
        'Mastersthesis', 'Misc', 'Phdthesis', 'Proceedings', 'Techreport', 'Unpublished'
    ];

    const months = [
        'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August',
        'September', 'October', 'November', 'December', 'not known'
    ];

    const submit = () => {
        router.post('/dashboard/publications', form, {
        onError: (err) => {
            console.log(err);
        },
  });
    };
    const edit = (publication) => {
    Object.assign(form, publication);
    };

    const destroy = (id) => {
        if (confirm('Are you sure?')) {
            router.delete(`/dashboard/publications/${id}`);
        }
    };
</script>

<style scoped>
input {
display: block;
}
</style>