<script setup>
import { ref, watch } from 'vue'
import { router, usePage } from '@inertiajs/vue3'

import Button from 'primevue/button';  // Import PrimeVue Button komponentu
import Dialog from 'primevue/dialog';  // Import PrimeVue Dialog komponentu
import HomeLayout from '@/Layouts/HomeLayout.vue';
const searchQuery = ref('')
const results = ref(usePage().props.results || [])
const years = usePage().props.years || []

// Reaktívna premenná pre zobrazenie dialogu
const showDialog = ref(false);

// Sledujeme zmeny vo vyhľadávaní a posielame AJAX request
watch(searchQuery, (value) => {
    router.get(route('home'), { query: value }, {
        preserveState: true,
        preserveScroll: true,
        only: ['results'],
        onSuccess: (page) => {
            results.value = page.props.results
        }
    })
})

defineOptions({
    layout: HomeLayout
})
</script>

<template>

    <div class="max-w-4xl mx-auto p-4">
        <div class="text-center">
            <!-- Tlačidlo z PrimeVue -->
            <Button label="Klikni ma!" class="p-button p-button-success" @click="showDialog = true" />
            
            <!-- Dialog z PrimeVue -->
            <Dialog v-model:visible="showDialog" header="Testovací dialog" :style="{ width: '50vw' }">
                <p>Obsah dialogu</p>
            </Dialog>
        </div>

        <h1 class="text-2xl font-bold mb-4">Publikácie podľa rokov</h1>

        <!-- Vyhľadávacie pole -->
        <div class="mb-4">
            <input 
                type="text" 
                v-model="searchQuery" 
                placeholder="Hľadať autora alebo publikáciu..."
                class="w-full p-2 border rounded-md shadow-sm"
            />
        </div>

        <!-- Výsledky vyhľadávania -->
        <div v-if="results.length" class="mt-4 bg-white p-4 shadow rounded">
            <h2 class="text-lg font-semibold">Výsledky</h2>
            <ul>
                <li v-for="result in results" :key="result.id" class="border-b py-2">
                    <span class="font-bold">{{ result.name }}</span> - {{ result.type }}
                </li>
            </ul>
        </div>

        <!-- Zoznam dostupných rokov -->
        <div class="mt-6">
            <h2 class="text-xl font-semibold">Dostupné roky</h2>
            <ul>
                <li v-for="year in years" :key="year" class="mt-2 p-2 bg-gray-100 rounded">
                    {{ year }}
                </li>
            </ul>
        </div>
    </div>
    
</template>
