<script setup>
import { ref } from "vue";
import { router } from "@inertiajs/vue3";
import Paginator from "primevue/paginator";
import Button from "primevue/button";
import PublicationFilter from "@/Components/PublicationFilter.vue";
import PublicationSortBar from "@/Components/PublicationSortBar.vue";

const props = defineProps({
    publications: Object,
    per_page: [Number, String],
    search: String,
    filters: Object,
    options: Object,
});

const currentPage = ref(props.publications?.current_page || 1);
const perPage = ref(Number(props.publications?.per_page) || 10);
const selectedInstitute = ref(props.filters?.institute || null);
const selectedAuthorId = ref(props.filters?.author_id || null);
const selectedYear = ref(props.filters?.year || null);
const selectedNumber = ref(props.filters?.number || null);
const sortKey = ref(props.filters?.sortKey || "title_asc");

const applyFilters = ({ page = 1, per_page = perPage.value } = {}) => {
    if (page === 1) currentPage.value = 1;
    router.get(
        route("search.index"),
        {
            typ: "publikacie",
            page,
            per_page,
            search: props.search || undefined,
            year: selectedYear.value || undefined,
            number: selectedNumber.value || undefined,
            institute: selectedInstitute.value || undefined,
            author_id: selectedAuthorId.value || undefined,
            sortKey: sortKey.value || undefined,
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
    selectedYear.value = null;
    selectedNumber.value = null;
    selectedInstitute.value = null;
    selectedAuthorId.value = null;
    applyFilters({ page: 1 });
};

const goPublication = (id) => router.get(route("publications.detail", id));
const goAuthor = (id) => router.get(route("authors.detail", id));
</script>

<template>
    <div>
        <div class="mb-8 pb-6 border-b border-gray-100">
            <PublicationFilter
                :options="options"
                v-model:year="selectedYear"
                v-model:number="selectedNumber"
                v-model:institute="selectedInstitute"
                v-model:authorId="selectedAuthorId"
                @apply="applyFilters"
                @clear="clearFilters"
            />
        </div>

        <div
            v-if="publications?.data?.length"
            class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6"
        >
            <p class="text-lg text-gray-500">
                Nájdených výsledkov:
                <span class="font-semibold text-gray-900">{{
                    publications.total
                }}</span>
            </p>
            <PublicationSortBar
                v-model:sortKey="sortKey"
                @change="applyFilters"
            />
        </div>

        <p
            v-if="!publications?.data?.length"
            class="text-center text-gray-500 py-10 text-lg"
        >
            Zadaným kritériám nevyhovuje žiadna publikácia.
        </p>

        <div
            v-if="publications?.data?.length"
            class="flex flex-col divide-y divide-gray-100"
        >
            <div
                v-for="pub in publications.data"
                :key="pub.id"
                class="py-4 flex items-start justify-between gap-6"
            >
                <div class="flex-1">
                    <Button
                        type="button"
                        @click="goPublication(pub.id)"
                        unstyled
                        class="text-left text-gray-900 font-bold hover:underline leading-snug text-xl"
                        :label="pub.title"
                    />
                    <div class="mt-2 text-lg text-gray-600">
                        <span
                            v-for="(author, i) in pub.authors"
                            :key="author.id"
                        >
                            <button
                                type="button"
                                @click.stop="goAuthor(author.id)"
                                class="hover:underline hover:text-gray-900 transition"
                            >
                                {{ author.firstname }}
                                {{ author.surname }}</button
                            ><span v-if="i < pub.authors.length - 1">, </span>
                        </span>
                    </div>
                </div>
                <div class="shrink-0 text-lg text-gray-400 font-medium pt-0.5">
                    <span v-if="pub.firstpage && pub.firstpage !== '0'">
                        Strana: {{ pub.firstpage
                        }}<span v-if="pub.lastpage && pub.lastpage !== '0'"
                            >–{{ pub.lastpage }}</span
                        >
                    </span>
                </div>
            </div>
        </div>

        <div
            v-if="publications?.data?.length && publications.total > perPage"
            class="mt-8"
        >
            <Paginator
                :rows="perPage"
                :totalRecords="publications.total"
                :rowsPerPageOptions="[10, 20, 30]"
                :first="(currentPage - 1) * perPage"
                :pageLinkSize="3"
                @page="changePage"
                class="!bg-transparent !p-0"
            />
        </div>
    </div>
</template>
