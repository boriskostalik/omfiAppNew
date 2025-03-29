<script setup>
import { router } from '@inertiajs/vue3';
const props = defineProps({
  author: Object,
});
const goToAuthor = (id) => {
    router.get(route('authors.detail', id));
}
</script>

<template>
  <div class="border p-4 rounded-lg shadow-md mb-4 cursor-pointer" @click="goToAuthor(author.id)">
    <h2 class="text-xl font-semibold">{{ author.firstname }} {{ author.surname }}</h2>
    <p v-if="author?.pivot?.is_editor === 'Y'" class="text-gray-500 italic">Editor</p>
    <div class="mt-2">
      <p class="text-gray-700">
        <strong>Email:</strong> 
        <a :href="'mailto:' + author.email" class="text-blue-600 hover:underline">{{ author.email }}</a>
      </p>

      <p v-if="author.institute" class="text-gray-700">
        <strong>Institute:</strong> {{ author.institute }}
      </p>
      <p v-else class="text-gray-500 italic">No institute specified</p>

      <p v-if="author.url" class="text-gray-700">
        <strong>Website:</strong> 
        <a :href="author.url" target="_blank" class="text-blue-600 hover:underline">{{ author.url }}</a>
      </p>
      <p v-else class="text-gray-500 italic">No website available</p>
    </div>
  </div>
</template>

<style scoped>
/* Scoped styles for the Author component */
</style>
