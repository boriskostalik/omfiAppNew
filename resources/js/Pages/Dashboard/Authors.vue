<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import { DataTable, Column, Paginator, Button, InputText } from 'primevue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import AuthorForm from '@/Pages/Dashboard/AuthorForm.vue'

defineOptions({ layout: AuthenticatedLayout })

const props = defineProps({
    authors: Object,
    search: String,
})

const searchQuery = ref(props.search || '')
const isModalVisible = ref(false)
const authorToEdit = ref(null)
const currentPage = ref(props.authors.current_page || 1)
const perPage = ref(Number(props.authors.per_page) || 10)

const submitSearch = () => {
    router.get(route('authors.dashboard'), { page: 1, search: searchQuery.value })
}

const changePage = (val) => {
    currentPage.value = val.page + 1
    perPage.value = val.rows
    router.get(route('authors.dashboard'), {
        page: currentPage.value,
        per_page: perPage.value,
        search: searchQuery.value,
    })
}

const closeModal = () => {
    isModalVisible.value = false
    authorToEdit.value = null
}

const onRowEdit = (id) => {
    authorToEdit.value = props.authors.data.find((a) => a.id === id)
    isModalVisible.value = true
}

const onRowDelete = (id) => {
    if (confirm('Naozaj vymazať autora?')) {
        router.delete(`/dashboard/authors/${id}`)
    }
}
</script>

<template>
    <div class="max-w-5xl mx-auto p-6">

        <!-- Hlavička -->
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Autori</h1>
            <Button label="Pridať autora" icon="pi pi-plus" @click="isModalVisible = true" />
        </div>

        <!-- Vyhľadávanie -->
        <div class="mb-4 flex gap-2">
            <InputText
                v-model="searchQuery"
                placeholder="Hľadať podľa mena..."
                class="w-full max-w-sm"
                @keyup.enter="submitSearch"
            />
            <Button label="Hľadať" icon="pi pi-search" @click="submitSearch" />
        </div>

        <!-- Tabuľka -->
        <DataTable
            :value="authors.data"
            dataKey="id"
            stripedRows
        >
            <Column field="firstname" header="Meno" sortable />
            <Column field="surname" header="Priezvisko" sortable />
            <Column field="institute" header="Inštitúcia" />
            <Column header="" style="width: 100px;">
                <template #body="{ data }">
                    <div class="flex gap-2 justify-end">
                        <Button icon="pi pi-pencil" @click="onRowEdit(data.id)" severity="secondary" rounded text />
                        <Button icon="pi pi-trash" @click="onRowDelete(data.id)" severity="danger" rounded text />
                    </div>
                </template>
            </Column>
            <template #empty>
                <p class="text-center text-gray-400 py-6">Žiadni autori nenájdení.</p>
            </template>
        </DataTable>

        <!-- Paginator -->
        <Paginator
            v-if="authors.total > perPage"
            :rows="perPage"
            :totalRecords="authors.total"
            :rowsPerPageOptions="[10, 20, 50]"
            :first="(currentPage - 1) * perPage"
            class="mt-4"
            @page="changePage"
        />
    </div>

    <AuthorForm
        :author="authorToEdit"
        :visible="isModalVisible"
        @close="closeModal"
    />
</template>