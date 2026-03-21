<script setup>
import { ref } from "vue";
import { router } from "@inertiajs/vue3";
import { Button, DataTable, Column, Paginator, Dialog } from "primevue";
import Header from "@/Components/Header.vue";
import PublicationForm from "@/Pages/Dashboard/PublicationForm.vue";

import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
defineOptions({
    layout: AuthenticatedLayout,
});

const props = defineProps({
    publications: Object,
    entered_by: Number,
    authors: Array,
    issues: Array,
    filters: Object,
    sortField: String,
    sortOrder: String,
});

const filters = ref({
    title: { value: props.filters?.title || null },
    type: { value: props.filters?.type || null },
    year: { value: props.filters?.year || null },
});

const currentPage = ref(props.publications.current_page || 1);
const perPage = ref(Number(props.publications.per_page) || 10);
const sortField = ref(props.sortField || "title");
const sortOrder = ref(props.sortOrder === "desc" ? -1 : 1);
const searchQuery = ref("");

const isModalVisible = ref(false);
const publicationToEdit = ref(null);
const deleteDialogVisible = ref(false);
const publicationToDelete = ref(null);

const closeModal = () => {
    isModalVisible.value = false;
    publicationToEdit.value = null;
};

const changePage = (val) => {
    currentPage.value = val.page + 1;
    perPage.value = val.rows;
    router.get(route("publications.dashboard"), syncParams());
};

const cleanParams = (params) => {
    return Object.fromEntries(
        Object.entries(params).filter(
            ([_, value]) => value != null && value !== "",
        ),
    );
};

const submitSearch = (query) => {
    searchQuery.value = query;
    router.get(route("publications.dashboard"), { ...syncParams(), page: 1, search: query });
};

const onRowDelete = (id) => {
    publicationToDelete.value = id;
    deleteDialogVisible.value = true;
};

const confirmDelete = () => {
    router.delete(`/dashboard/publications/${publicationToDelete.value}`);
    deleteDialogVisible.value = false;
    publicationToDelete.value = null;
};

const syncParams = () => {
    return cleanParams({
        page: currentPage.value,
        per_page: perPage.value,
        sortField: sortField.value,
        sortOrder: sortOrder.value === 1 ? "asc" : "desc",
        search: searchQuery.value,
        title: filters.value.title?.value,
        type: filters.value.type?.value,
        year: filters.value.year?.value,
        journal: filters.value.journal?.value,
    });
};

const onSort = (event) => {
    sortField.value = event.sortField;
    sortOrder.value = event.sortOrder;

    router.get(route("publications.dashboard"), syncParams());
};

const onRowEdit = (id) => {
    isModalVisible.value = true;
    publicationToEdit.value = props.publications.data.find((p) => p.id === id);
};
</script>

<template>
    <div class="max-w-8xl mx-auto px-6 py-4">
        <Header
            title="Publikácie"
            has-search
            @search="(query) => submitSearch(query)"
        />
        <DataTable
            v-model:filters="filters"
            :value="publications.data"
            :sortField="sortField"
            :sortOrder="sortOrder"
            dataKey="id"
            lazy
            @sort="onSort"
        >
            <Column field="title" header="Title" sortable style="width: 50%">
                <template #body="{ data }">
                    {{ data.title }}
                </template>
            </Column>

            <Column field="year" header="Year" sortable style="width: 10%">
                <template #body="{ data }">
                    {{ data.issue?.year ?? "—" }}
                </template>
            </Column>

            <Column header="Vydanie" style="width: 10%">
                <template #body="{ data }">
                    {{ data.issue?.number ?? "—" }}
                </template>
            </Column>

            <Column header="Authors" style="width: 25%">
                <template #body="{ data }">
                    <span v-if="data.authors.length">{{
                        data.authors.map((a) => `${a.cleanname}`).join(" ")
                    }}</span>
                    <span v-else>No authors</span>
                </template>
            </Column>
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

            <template #empty> No publications found. </template>
        </DataTable>
        <Paginator
            v-if="publications.next_page_url || publications.prev_page_url"
            :rows="perPage"
            :totalRecords="publications.total"
            :rowsPerPageOptions="[10, 20, 30]"
            :first="(currentPage - 1) * perPage"
            @page="changePage"
        />
    </div>
    <Dialog
        v-model:visible="deleteDialogVisible"
        header="Vymazať publikáciu"
        :style="{ width: '25rem' }"
        modal
    >
        <p class="mb-4">Naozaj chcete vymazať túto publikáciu?</p>
        <div class="flex justify-end gap-2">
            <Button label="Zrušiť" severity="secondary" @click="deleteDialogVisible = false" />
            <Button label="Vymazať" severity="danger" @click="confirmDelete" />
        </div>
    </Dialog>
    <PublicationForm
        :publication="publicationToEdit"
        :visible="isModalVisible"
        :entered_by="entered_by"
        :authors="authors"
        :issues="issues"
        @close="closeModal()"
    />
</template>
