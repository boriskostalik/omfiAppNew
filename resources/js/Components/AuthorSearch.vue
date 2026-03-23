<script setup>
import { ref } from "vue";
import { router } from "@inertiajs/vue3";
import Paginator from "primevue/paginator";
import Select from "primevue/select";
import Button from "primevue/button";

const props = defineProps({
    authors: Object,
    per_page: [Number, String],
    search: String,
    filters: Object,
    options: Object,
});

const sortOptions = [
    { label: "Meno A–Z", value: "name_asc" },
    { label: "Meno Z–A", value: "name_desc" },
];

const currentPage = ref(props.authors?.current_page || 1);
const perPage = ref(Number(props.authors?.per_page) || 10);
const selectedInstitute = ref(props.filters?.institute || null);
const sortKey = ref(props.filters?.sortKey || "name_asc");

const applyFilters = ({ page = 1, per_page = perPage.value } = {}) => {
    if (page === 1) currentPage.value = 1;
    router.get(
        route("search.index"),
        {
            typ: "autori",
            page,
            per_page,
            search: props.search || undefined,
            institute: selectedInstitute.value || undefined,
            sortKey: sortKey.value,
        },
        { preserveState: true, preserveScroll: true, replace: true },
    );
};

const changePage = (val) => {
    currentPage.value = val.page + 1;
    perPage.value = val.rows;
    applyFilters({ page: currentPage.value, per_page: perPage.value });
};

const clearFilters = () => {
    selectedInstitute.value = null;
    applyFilters({ page: 1 });
};

const goAuthor = (id) => router.get(route("authors.detail", id));
</script>

<template>
    <div>
        <div class="mb-8 pb-6 border-b border-gray-100">
            <div class="flex flex-wrap items-end gap-x-8 gap-y-4">
                <Select
                    v-model="selectedInstitute"
                    :options="options?.institutes ?? []"
                    placeholder="Inštitút"
                    filter
                    showClear
                    class="w-64"
                    @change="applyFilters({ page: 1 })"
                    :pt="{
                        root: { class: '!rounded-xl' },
                        label: { class: '!py-[0.6rem]' },
                        overlay: {
                            style: 'width:min(300px, calc(100vw - 2rem)); max-width:min(300px, calc(100vw - 2rem));',
                        },
                        listContainer: {
                            style: 'overflow-x:auto; overflow-y:auto; -webkit-overflow-scrolling:touch; touch-action:pan-x pan-y;',
                        },
                        list: { style: 'width:max-content; min-width:100%;' },
                        option: { style: 'min-width:max-content;' },
                        optionLabel: {
                            style: 'white-space:nowrap; display:inline-block;',
                        },
                    }"
                />
                <Button
                    label="Zrušiť filtre"
                    severity="danger"
                    @click="clearFilters"
                    class="!rounded-xl"
                />
            </div>
        </div>

        <div
            v-if="authors?.data?.length"
            class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6"
        >
            <p class="text-lg text-gray-500">
                Nájdených výsledkov:
                <span class="font-semibold text-gray-900">{{
                    authors.total
                }}</span>
            </p>
            <Select
                v-model="sortKey"
                :options="sortOptions"
                optionLabel="label"
                optionValue="value"
                class="w-auto select-underline"
                @change="applyFilters({ page: 1 })"
            />
        </div>

        <p
            v-if="!authors?.data?.length"
            class="text-center text-gray-500 py-10 text-lg"
        >
            Nenašiel sa žiadny autor.
        </p>

        <div
            v-if="authors?.data?.length"
            class="flex flex-col divide-y divide-gray-100"
        >
            <div v-for="author in authors.data" :key="author.id" class="py-4">
                <button
                    type="button"
                    @click="goAuthor(author.id)"
                    class="text-left text-gray-900 font-bold hover:underline leading-snug text-xl"
                >
                    {{
                        author.cleanname?.replace(",", "") ??
                        `${author.firstname} ${author.surname}`
                    }}
                </button>
                <p v-if="author.institute" class="mt-1 text-lg text-gray-500">
                    {{ author.institute }}
                </p>
            </div>
        </div>

        <div
            v-if="authors?.data?.length && authors.total > perPage"
            class="mt-8"
        >
            <Paginator
                :rows="perPage"
                :totalRecords="authors.total"
                :rowsPerPageOptions="[10, 20, 30]"
                :first="(currentPage - 1) * perPage"
                :pageLinkSize="3"
                @page="changePage"
                class="!bg-transparent !p-0"
                :pt="{
                    pcRowPerPageDropdown: {
                        root: { class: '!rounded-xl !bg-[#1E4E8C] !border-[#1E4E8C]' },
                        label: { class: '!py-[0.6rem] !text-white !font-semibold' },
                        dropdown: { class: '!text-white' },
                    },
                }"
            />
        </div>
    </div>
</template>
