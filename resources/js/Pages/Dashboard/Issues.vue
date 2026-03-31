<script setup>
import { ref } from "vue";
import { router } from "@inertiajs/vue3";
import { Button, DataTable, Column, Paginator, Dialog } from "primevue";
import Header from "@/Components/Header.vue";
import IssueForm from "@/Pages/Dashboard/IssueForm.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps({
    issues: Object,
    search: String,
    sortField: String,
    sortOrder: String,
});

const currentPage = ref(props.issues.current_page || 1);
const perPage = ref(Number(props.issues.per_page) || 20);
const sortField = ref(props.sortField || "year");
const sortOrder = ref(props.sortOrder === "asc" ? 1 : -1);
const searchQuery = ref(props.search || "");

const isModalVisible = ref(false);
const issueToEdit = ref(null);
const deleteDialogVisible = ref(false);
const issueToDelete = ref(null);

const closeModal = () => {
    isModalVisible.value = false;
    issueToEdit.value = null;
};

const syncParams = () => {
    const params = {
        page: currentPage.value,
        per_page: perPage.value,
        sortField: sortField.value,
        sortOrder: sortOrder.value === 1 ? "asc" : "desc",
    };
    if (searchQuery.value) params.search = searchQuery.value;
    return params;
};

const changePage = (val) => {
    currentPage.value = val.page + 1;
    perPage.value = val.rows;
    router.get(route("issues.dashboard"), syncParams());
};

const onSort = (event) => {
    sortField.value = event.sortField;
    sortOrder.value = event.sortOrder;
    currentPage.value = 1;
    router.get(route("issues.dashboard"), syncParams());
};

const submitSearch = (query) => {
    searchQuery.value = query;
    router.get(route("issues.dashboard"), {
        ...syncParams(),
        page: 1,
        search: query,
    });
};

const onRowEdit = (issue) => {
    issueToEdit.value = issue;
    isModalVisible.value = true;
};

const onRowDelete = (id) => {
    issueToDelete.value = id;
    deleteDialogVisible.value = true;
};

const confirmDelete = () => {
    router.delete(route("issues.destroy", issueToDelete.value));
    deleteDialogVisible.value = false;
    issueToDelete.value = null;
};
</script>

<template>
    <div class="max-w-6xl mx-auto px-6 py-4">
        <Header
            title="Vydania"
            has-search
            @search="(query) => submitSearch(query)"
        />
        <DataTable
            :value="issues.data"
            :sortField="sortField"
            :sortOrder="sortOrder"
            dataKey="id"
            lazy
            @sort="onSort"
        >
            <Column field="year" header="Rok" sortable style="width: 20%" />
            <Column field="number" header="Číslo" style="width: 20%" />
            <Column field="volume" header="Volume" style="width: 20%" />
            <Column header="PDF" style="width: 10%">
                <template #body="{ data }">
                    <a
                        v-if="data.pdf_path"
                        :href="`/storage/${data.pdf_path}`"
                        target="_blank"
                        class="text-[#1E4E8C] hover:underline"
                    >
                        <i class="pi pi-file-pdf text-lg"></i>
                    </a>
                    <span v-else class="text-gray-300">—</span>
                </template>
            </Column>
            <Column header="Publikácií" style="width: 15%">
                <template #body="{ data }">
                    {{ data.publications_count ?? 0 }}
                </template>
            </Column>
            <Column style="width: 15%">
                <template #header>
                    <div class="flex w-full justify-end">
                        <Button
                            icon="pi pi-plus"
                            label="Pridať"
                            severity="success"
                            @click="isModalVisible = true"
                        />
                    </div>
                </template>
                <template #body="{ data }">
                    <div class="flex gap-2 justify-end">
                        <Button
                            icon="pi pi-pencil"
                            @click="onRowEdit(data)"
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
            <template #empty>Žiadne vydania.</template>
        </DataTable>
        <Paginator
            v-if="issues.next_page_url || issues.prev_page_url"
            :rows="perPage"
            :totalRecords="issues.total"
            :rowsPerPageOptions="[10, 20, 50]"
            :first="(currentPage - 1) * perPage"
            @page="changePage"
            :pageLinkSize="3"
        />
    </div>
    <Dialog
        v-model:visible="deleteDialogVisible"
        header="Vymazať vydanie"
        :style="{ width: '25rem' }"
        modal
    >
        <p class="mb-4">Naozaj chcete vymazať toto vydanie?</p>
        <div class="flex justify-end gap-2">
            <Button
                label="Zrušiť"
                severity="secondary"
                @click="deleteDialogVisible = false"
            />
            <Button label="Vymazať" severity="danger" @click="confirmDelete" />
        </div>
    </Dialog>
    <IssueForm
        :issue="issueToEdit"
        :visible="isModalVisible"
        @close="closeModal()"
    />
</template>
