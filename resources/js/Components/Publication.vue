<script setup>
import { router } from '@inertiajs/vue3';
const props = defineProps(['publication', 'withAuthors']);
const goToAuthor = (id) => {
    router.get(route('authors.detail', id));
}

const goToPublication = (id) => {
    router.get(route('publications.detail', id));
}

</script>

<template>
    <div class="p-6 bg-white rounded-2xl shadow-lg">
        <h2 class="text-xl font-semibold text-gray-800">
            <span class="italic">
                <span v-for="(author, index) in publication.authors" :key="author.id">
                    <span class="cursor-pointer" @click="goToAuthor(author.id)">{{ author.cleanname }}</span>
                    <span v-if="author.is_editor === 'Y'">(Editor)</span>
                    <span v-if="index < publication.authors.length - 1">, </span>
                    </span>
                </span>
            <span @click="goToPublication(publication.id)" class="cursor-pointer text-blue-600 italic ml-1 hover:text-blue-950">{{ publication.title }}</span>
        </h2>
    <p class="text-gray-600 mt-2">
      in: <span class="font-semibold">{{ publication.journal }}</span>,
      volume {{ publication.volume }}, number {{ publication.number }}, pages {{ publication.firstpage }}-{{ publication.lastpage }},
      ISSN {{ publication.issn }}, {{ publication.year }}.
    </p>
  </div>
</template>
<style lang="scss" scoped>

</style>