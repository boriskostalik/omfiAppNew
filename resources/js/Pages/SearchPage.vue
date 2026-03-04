<script setup>
import { ref } from "vue";
import { router } from "@inertiajs/vue3";
import HomeLayout from "@/Layouts/HomeLayout.vue";
import Button from "primevue/button";
import PublicationSearch from "@/Components/PublicationSearch.vue";
import AuthorSearch from "@/Components/AuthorSearch.vue";

defineOptions({ layout: HomeLayout });

const props = defineProps({
    typ: String,
    publications: Object,
    per_page: [Number, String],
    search: String,
    filters: Object,
    options: Object,
    authors: Object,
});

const typeOptions = [
    { label: "Publikácie", value: "publikacie" },
    { label: "Autori", value: "autori" },
];

const selectedType = ref(props.typ ?? "publikacie");
const searchValue = ref(props.search ?? "");

const selectType = (value) => {
    selectedType.value = value;
    router.get(route("search.index"), { typ: value }, { replace: true });
};

const doSearch = () => {
    router.get(
        route("search.index"),
        { typ: selectedType.value, search: searchValue.value || undefined },
        { replace: true },
    );
};
</script>

<template>
    <div class="max-w-4xl mx-auto px-4 py-14">
        <div class="mb-10">
            <h1 class="text-4xl font-bold text-gray-900 mb-5">Vyhľadávanie</h1>

            <div class="flex items-center gap-4 mb-5">
                <span class="text-base text-gray-400">Prehľadávať v</span>
                <div class="flex gap-2">
                    <Button
                        v-for="opt in typeOptions"
                        :key="opt.value"
                        :label="opt.label"
                        unstyled
                        @click="selectType(opt.value)"
                        :class="selectedType === opt.value
                            ? 'bg-[#1E4E8C] text-white'
                            : 'bg-white text-gray-600 border border-gray-200 hover:border-gray-300 hover:text-gray-900'"
                        class="px-5 py-2 rounded-xl text-base font-medium transition-colors"
                    />
                </div>
            </div>

            <div class="flex items-center bg-white rounded-2xl border border-gray-200 shadow-sm focus-within:ring-2 focus-within:ring-blue-100 focus-within:border-blue-300 transition-all">
                <svg class="ml-4 w-5 h-5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input
                    v-model="searchValue"
                    type="text"
                    :placeholder="selectedType === 'publikacie' ? 'Hľadať publikácie...' : 'Hľadať autorov...'"
                    class="flex-1 px-4 py-4 bg-transparent outline-none border-0 ring-0 text-gray-900 placeholder-gray-400"
                    @keyup.enter="doSearch"
                />
                <Button
                    label="Hľadať"
                    unstyled
                    @click="doSearch"
                    class="mx-2 px-5 py-2.5 bg-[#1E4E8C] hover:bg-[#184073] text-white text-sm font-semibold rounded-xl transition-colors shrink-0"
                />
            </div>
        </div>
        <PublicationSearch
            v-if="typ === 'publikacie'"
            :publications="publications"
            :per_page="per_page"
            :search="search"
            :filters="filters"
            :options="options"
        />
        <AuthorSearch
            v-else
            :authors="authors"
            :per_page="per_page"
            :search="search"
            :filters="filters"
            :options="options"
        />
    </div>
</template>
