<template>
    <div class="page-header flex justify-between items-center">
        <h1 class="text-3xl font-bold mt-2 mb-4">{{ title }}</h1>
        <div class="flex h-12" v-if="hasSearch">
            <InputGroup>
                <InputText  
                    v-model="searchQuery"
                    @keyup.enter="$emit('search', searchQuery)"
                    placeholder="Vyhľadávanie"
                    class="p-2 border rounded w-full mb-4" 
                />
                <InputGroupAddon @click="$emit('search', searchQuery)" class="cursor-pointer">
                    <i class="pi pi-search"></i>
                </InputGroupAddon>
            </InputGroup>
        </div>
    </div>
</template>
<script setup>
import { ref } from 'vue';
import { InputText, InputGroup, InputGroupAddon} from 'primevue';

const props = defineProps({
    title: String,
    hasSearch: {
        type: Boolean,
        default: false,
    },
})

const searchQuery = ref(window.location.search.split('search=')[1] || '');


</script>