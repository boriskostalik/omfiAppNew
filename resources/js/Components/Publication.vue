<script setup>
import { router } from '@inertiajs/vue3';
const props = defineProps(['publication', 'withAuthors']);
const goToAuthor = (id) => {
    console.log('Go to author');
    router.get(route('authors.detail', id));
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
            <span class="text-blue-600 italic">{{ publication.titleEng }}</span>
        </h2>
    <p class="text-gray-600 mt-2">
      in: <span class="font-semibold">{{ publication.journal }}</span>,
      volume {{ publication.volume }}, number {{ publication.number }}, pages {{ publication.firstpage }}-{{ publication.lastpage }},
      ISSN {{ publication.issn }}, {{ publication.year }}.
    </p>
  </div>
    <!-- <div class="border p-4 rounded-lg shadow-md">
        <h2 class="text-xl font-semibold">{{ publication.title }}</h2>
        <div v-if="withAuthors" class="mt-2">
            <p v-if="publication.authors.length" class="text-gray-700">
                <strong>Autori:</strong>
                <span v-for="(author, index) in publication.authors" :key="author.id">
                    {{ author.name }} 
                    <span v-if="author.is_editor === 'Y'">(Editor)</span>
                    <span v-if="index < publication.authors.length - 1">, </span>
                </span>
            </p>
            <p v-else class="text-gray-500 italic">Autor neznámy</p>
        </div>
    </div> -->
</template>
<style lang="scss" scoped>

</style>