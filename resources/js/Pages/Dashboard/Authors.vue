<script setup>
import { ref } from "vue";
import { router } from "@inertiajs/vue3";
import { Button, DataTable, Column, Paginator, Dialog } from "primevue";
import Header from "@/Components/Header.vue";
import AuthorForm from "@/Pages/Dashboard/AuthorForm.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps({
    authors: Object,
    institutes: Array,
    search: String,
    sortField: String,
    sortOrder: String,
});

const currentPage = ref(props.authors.current_page || 1);
const perPage = ref(Number(props.authors.per_page) || 10);
const sortField = ref(props.sortField || "id");
const sortOrder = ref(props.sortOrder === "asc" ? 1 : -1);
const searchQuery = ref(props.search || "");

const isModalVisible = ref(false);
const authorToEdit = ref(null);
const deleteDialogVisible = ref(false);
const authorToDelete = ref(null);

const closeModal = () => {
    isModalVisible.value = false;
    authorToEdit.value = null;
};

const syncParams = () => ({
    page: currentPage.value,
    per_page: perPage.value,
    sortField: sortField.value,
    sortOrder: sortOrder.value === 1 ? "asc" : "desc",
    ...(searchQuery.value ? { search: searchQuery.value } : {}),
});

const changePage = (val) => {
    currentPage.value = val.page + 1;
    perPage.value = val.rows;
    router.get(route("authors.dashboard"), syncParams());
};

const submitSearch = (query) => {
    searchQuery.value = query;
    router.get(route("authors.dashboard"), { ...syncParams(), page: 1 });
};

const onSort = (event) => {
    sortField.value = event.sortField;
    sortOrder.value = event.sortOrder;
    currentPage.value = 1;
    router.get(route("authors.dashboard"), syncParams());
};

const onRowEdit = (id) => {
    authorToEdit.value = props.authors.data.find((a) => a.id === id);
    isModalVisible.value = true;
};

const onRowDelete = (id) => {
    authorToDelete.value = id;
    deleteDialogVisible.value = true;
};

const confirmDelete = () => {
    router.delete(`/dashboard/authors/${authorToDelete.value}`);
    deleteDialogVisible.value = false;
    authorToDelete.value = null;
};
</script>

<template>
    <div class="max-w-8xl mx-auto px-6 py-4">
        <Header
            title="Autori"
            has-search
            @search="(query) => submitSearch(query)"
        />
        <DataTable
            :value="authors.data"
            :sortField="sortField"
            :sortOrder="sortOrder"
            dataKey="id"
            lazy
            @sort="onSort"
        >
            <Column field="id" header="ID" sortable style="width: 5%" />
            <Column
                field="surname"
                header="Priezvisko"
                sortable
                style="width: 25%"
            />
            <Column
                field="firstname"
                header="Meno"
                sortable
                style="width: 20%"
            />
            <Column field="von" header="Von" style="width: 10%" />
            <Column header="Inštitúcia" style="width: 25%">
                <template #body="{ data }">
                    {{ data.institute?.name ?? "—" }}
                </template>
            </Column>
            <Column field="email" header="Email" style="width: 15%" />
            <Column class="w-24 !text-end">
                <template #header>
                    <Button
                        icon="pi pi-plus"
                        label="Pridať"
                        severity="success"
                        @click="isModalVisible = true"
                    />
                </template>
                <template #body="{ data }">
                    <div class="flex gap-2 justify-end">
                        <Button
                            icon="pi pi-pencil"
                            @click="onRowEdit(data.id)"
                            severity="secondary"
                            rounded
                        />
                        <Button
                            icon="pi pi-trash"
                            @click="onRowDelete(data.id)"
                            severity="danger"
                            rounded
                        />
                    </div>
                </template>
            </Column>
            <template #empty>Žiadni autori.</template>
        </DataTable>
        <Paginator
            v-if="authors.next_page_url || authors.prev_page_url"
            :rows="perPage"
            :totalRecords="authors.total"
            :rowsPerPageOptions="[10, 20, 50]"
            :first="(currentPage - 1) * perPage"
            @page="changePage"
            :pageLinkSize="3"
        />
    </div>
    <Dialog
        v-model:visible="deleteDialogVisible"
        header="Vymazať autora"
        :style="{ width: '25rem' }"
        modal
    >
        <p class="mb-4">Naozaj chcete vymazať tohto autora?</p>
        <div class="flex justify-end gap-2">
            <Button
                label="Zrušiť"
                severity="secondary"
                @click="deleteDialogVisible = false"
            />
            <Button label="Vymazať" severity="danger" @click="confirmDelete" />
        </div>
    </Dialog>
    <AuthorForm
        :author="authorToEdit"
        :visible="isModalVisible"
        :institutes="institutes"
        @close="closeModal()"
    />
</template>
