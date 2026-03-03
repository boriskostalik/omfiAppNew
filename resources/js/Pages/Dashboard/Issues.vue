<script setup>
import { ref } from "vue";
import { router, useForm } from "@inertiajs/vue3";
import {
    DataTable,
    Column,
    Paginator,
    Button,
    Dialog,
    InputText,
    InputNumber,
    DatePicker,
    FileUpload,
    ToggleSwitch,
} from "primevue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps({
    issues: Object,
});

const isModalVisible = ref(false);
const issueToEdit = ref(null);

const form = useForm({
    year: null,
    volume: "",
    number: null,
    published_at: null,
    pdf: null,
    remove_pdf: false,
});

const openCreate = () => {
    issueToEdit.value = null;
    form.reset();
    isModalVisible.value = true;
};

const openEdit = (issue) => {
    issueToEdit.value = issue;
    form.year = issue.year;
    form.volume = issue.volume || "";
    form.number = issue.number;
    form.published_at = issue.published_at
        ? new Date(issue.published_at)
        : null;
    form.pdf = null;
    form.remove_pdf = false;
    isModalVisible.value = true;
};

const closeModal = () => {
    isModalVisible.value = false;
    issueToEdit.value = null;
    form.reset();
};

const onFileSelect = (e) => {
    form.pdf = e.files[0];
};

const submit = () => {
    const year = form.year ? parseInt(form.year) : null;
    const number = form.number ? parseInt(form.number) : null;
    const published_at = form.published_at
        ? form.published_at instanceof Date
            ? form.published_at.toISOString().slice(0, 10)
            : form.published_at
        : null;

    if (issueToEdit.value) {
        router.post(
            route("issues.update", issueToEdit.value.id),
            {
                _method: "PUT",
                year,
                number,
                volume: form.volume || null,
                published_at,
                pdf: form.pdf || null,
                remove_pdf: form.remove_pdf ? "true" : "false",
            },
            {
                forceFormData: true,
                onSuccess: () => closeModal(),
                onError: (e) => console.log(e),
            },
        );
    } else {
        router.post(
            route("issues.store"),
            {
                year,
                number,
                volume: form.volume || null,
                published_at,
                pdf: form.pdf || null,
            },
            {
                forceFormData: true,
                onSuccess: () => closeModal(),
                onError: (e) => console.log(e),
            },
        );
    }
};

const deleteIssue = (id) => {
    if (confirm("Naozaj vymazať issue?")) {
        router.delete(route("issues.destroy", id));
    }
};

const currentPage = ref(props.issues.current_page || 1);
const perPage = ref(props.issues.per_page || 20);

const changePage = (val) => {
    currentPage.value = val.page + 1;
    perPage.value = val.rows;
    router.get(route("issues.dashboard"), {
        page: currentPage.value,
        per_page: perPage.value,
    });
};
</script>

<template>
    <div class="max-w-5xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Vydania</h1>
            <Button
                label="Pridať vydanie"
                icon="pi pi-plus"
                @click="openCreate"
            />
        </div>
        <DataTable :value="issues.data" dataKey="id" stripedRows>
            <Column field="year" header="Rok" sortable style="width: 100px" />
            <Column field="number" header="Číslo" style="width: 100px" />
            <Column field="volume" header="Volume" style="width: 120px" />
            <Column field="published_at" header="Vydané" style="width: 140px">
                <template #body="{ data }">
                    {{
                        data.published_at ? data.published_at.slice(0, 10) : "—"
                    }}
                </template>
            </Column>
            <Column header="PDF" style="width: 80px">
                <template #body="{ data }">
                    <a
                        v-if="data.pdf_path"
                        :href="`/storage/${data.pdf_path}`"
                        target="_blank"
                        class="text-[#1E4E8C] hover:underline text-sm"
                    >
                        <i class="pi pi-file-pdf text-lg"></i>
                    </a>
                    <span v-else class="text-gray-300 text-sm">—</span>
                </template>
            </Column>
            <Column header="Publikácií" style="width: 110px">
                <template #body="{ data }">
                    {{ data.publications_count }}
                </template>
            </Column>
            <Column header="" style="width: 100px">
                <template #body="{ data }">
                    <div class="flex gap-2 justify-end">
                        <Button
                            icon="pi pi-pencil"
                            @click="openEdit(data)"
                            severity="secondary"
                            rounded
                            text
                        />
                        <Button
                            icon="pi pi-trash"
                            @click="deleteIssue(data.id)"
                            severity="danger"
                            rounded
                            text
                        />
                    </div>
                </template>
            </Column>
            <template #empty>
                <p class="text-center text-gray-400 py-6">Žiadne vydania</p>
            </template>
        </DataTable>

        <Paginator
            v-if="issues.total > perPage"
            :rows="perPage"
            :totalRecords="issues.total"
            :rowsPerPageOptions="[10, 20, 50]"
            :first="(currentPage - 1) * perPage"
            class="mt-4"
            @page="changePage"
        />
        <Dialog
            v-model:visible="isModalVisible"
            modal
            :header="issueToEdit ? 'Upraviť vydanie' : 'Pridať vydanie'"
            :style="{ width: '480px' }"
            @hide="closeModal"
        >
            <div class="flex flex-col gap-4 mt-2">
                <div class="grid grid-cols-2 gap-4">
                    <div class="flex flex-col gap-1">
                        <label class="text-sm font-medium text-gray-600"
                            >Rok *</label
                        >
                        <InputNumber
                            v-model="form.year"
                            placeholder="2024"
                            :useGrouping="false"
                        />
                        <small v-if="form.errors.year" class="text-red-500">{{
                            form.errors.year
                        }}</small>
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-sm font-medium text-gray-600"
                            >Číslo *</label
                        >
                        <InputNumber
                            v-model="form.number"
                            placeholder="1"
                            :useGrouping="false"
                        />
                        <small v-if="form.errors.number" class="text-red-500">{{
                            form.errors.number
                        }}</small>
                    </div>
                </div>

                <div class="flex flex-col gap-1">
                    <label class="text-sm font-medium text-gray-600"
                        >Volume</label
                    >
                    <InputText v-model="form.volume" placeholder="napr. 51" />
                </div>

                <div class="flex flex-col gap-1">
                    <label class="text-sm font-medium text-gray-600"
                        >Dátum vydania</label
                    >
                    <DatePicker
                        v-model="form.published_at"
                        dateFormat="yy-mm-dd"
                        showIcon
                    />
                </div>
                <div class="flex flex-col gap-2">
                    <label class="text-sm font-medium text-gray-600">PDF</label>

                    <div
                        v-if="issueToEdit?.pdf_path && !form.remove_pdf"
                        class="flex items-center justify-between bg-gray-50 border border-gray-200 rounded px-3 py-2"
                    >
                        <a
                            :href="`/storage/${issueToEdit.pdf_path}`"
                            target="_blank"
                            class="text-sm text-[#1E4E8C] hover:underline flex items-center gap-2"
                        >
                            <i class="pi pi-file-pdf"></i>
                            Zobraziť PDF
                        </a>
                        <div
                            class="flex items-center gap-2 text-sm text-gray-500"
                        >
                            <span>Odstrániť</span>
                            <ToggleSwitch v-model="form.remove_pdf" />
                        </div>
                    </div>

                    <FileUpload
                        v-if="!issueToEdit?.pdf_path || form.remove_pdf"
                        mode="basic"
                        accept=".pdf"
                        :maxFileSize="20000000"
                        chooseLabel="Vybrať PDF"
                        @select="onFileSelect"
                    />
                </div>

                <small v-if="form.errors.pdf" class="text-red-500">{{
                    form.errors.pdf
                }}</small>
            </div>

            <template #footer>
                <Button label="Zrušiť" text @click="closeModal" />
                <Button
                    :label="issueToEdit ? 'Uložiť' : 'Vytvoriť'"
                    icon="pi pi-check"
                    :loading="form.processing"
                    @click="submit"
                />
            </template>
        </Dialog>
    </div>
</template>
