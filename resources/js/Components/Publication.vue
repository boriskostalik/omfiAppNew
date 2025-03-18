<script setup>
const props = defineProps(['publication', 'withAuthors']);
console.log(props.publication);
</script>

<template>
    <div class="border p-4 rounded-lg shadow-md">
        <h2 class="text-xl font-semibold">{{ publication.title }}</h2>
          
          <!-- Zobrazenie autorov -->
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
    </div>
</template>
<style lang="scss" scoped>

</style>