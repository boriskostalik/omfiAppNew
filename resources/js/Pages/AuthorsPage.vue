<template>
    <Header title="Autori" hasSearch @search="query => submitSearch(query)" />
    
    <Author 
      v-for="author in authors.data" 
      :key="author.id" 
      :author="author" 
      @click="handleAuthorClick(author.id)"
    />
    
    <div class="flex justify-center w-full">
      <Paginator
        :rows="perPage"
        :totalRecords="authors.total"
        :rowsPerPageOptions="[10, 20, 30]"
        :first="(currentPage - 1) * perPage"
        @page="changePage"
      />
    </div>
</template>

<script setup>
import HomeLayout from '@/Layouts/HomeLayout.vue';
import Author from '@/Components/Author.vue';
import Header from '@/Components/Header.vue';
import Paginator from 'primevue/paginator';
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
  authors: Object,
});



const currentPage = ref(props.authors.current_page || 1);
const perPage = ref(Number(props.authors.per_page) || 10);

const handleAuthorClick = (id) => {
  router.get(route('authors.detail', id));
};

const changePage = (val) => {
  currentPage.value = val.page + 1;
  perPage.value = val.rows;

  router.get(route('authors.index'), {
    page: currentPage.value,
    per_page: perPage.value,
  });
};

const submitSearch = (query) => {
  router.get(route('authors.index'), {
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
