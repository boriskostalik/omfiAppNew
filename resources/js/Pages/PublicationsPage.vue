<template>
    <Header title="Publikácie" hasSearch @search="query => submitSearch(query)"/>
      <Publication 
        v-for="publication in publications.data" 
        :key="publication.id" 
        :publication="publication" 
      />

    <div class="flex justify-center w-full">
      <Paginator
        :rows="perPage"
        :totalRecords="publications.total"
        :rowsPerPageOptions="[10, 20, 30]"
        :first="(currentPage - 1) * perPage"
        @page="changePage"
      />
    </div>
</template>

<script setup>
import HomeLayout from '@/Layouts/HomeLayout.vue';
import Publication from '@/Components/Publication.vue';
import Header from '@/Components/Header.vue';
import Paginator from 'primevue/paginator';
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
  publications: Object,
});

defineOptions({
  layout: HomeLayout,
});

const currentPage = ref(props.publications.current_page || 1);
const perPage = ref(Number(props.publications.per_page) || 10);

const changePage = (val) => {
  currentPage.value = val.page + 1;
  perPage.value = val.rows;

  router.get(route('publications.index'), {
    page: currentPage.value,
    per_page: perPage.value,
  });
};

const submitSearch = (query) => {
  router.get(route('publications.index'), {
    page: 1,
    search: query,
  });
};

</script>

<style scoped>
/* Ensure paginator styles are applied */
.p-paginator .p-component {
  background-color: white !important;
}
</style>
