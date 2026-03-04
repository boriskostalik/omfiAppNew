<template>
    <Select
        v-model="sortKey"
        :options="options"
        optionLabel="label"
        optionValue="value"
        placeholder="Zoradiť"
        class="w-48 select-underline"
        @change="emit('change', { page: 1 })"
    />
</template>

<script setup>
import Select from "primevue/select";
const emit = defineEmits(["change"]);
const sortKey = defineModel("sortKey");
const options = [
    { label: "Názov A → Z", value: "title_asc" },
    { label: "Názov Z → A", value: "title_desc" },
];
</script>
