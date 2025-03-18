<template>
    <div class="page-header flex justify-between items-center">
        <h1 class="text-3xl font-bold mt-2 mb-4">{{ title }}</h1>
        <div v-if="hasSearch">
            <InputText  
                v-model="searchQuery"
                @keyup.enter="$emit('search', searchQuery)"
                placeholder="Search publications..."
                class="p-2 border rounded w-full mb-4" 
            />
        </div>
    </div>
</template>
<script setup>
import { ref } from 'vue';
import InputText from 'primevue/inputtext';

const props = defineProps({
    title: String,
    hasSearch: {
        type: Boolean,
        default: false,
    },
})

const searchQuery = ref(window.location.search.split('search=')[1] || '');


</script>