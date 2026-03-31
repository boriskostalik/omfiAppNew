<script setup>
import Select from "primevue/select";
import Button from "primevue/button";

defineProps({
    options: { type: Object, required: true },
});

const emit = defineEmits(["apply", "clear"]);
const year = defineModel("year");
const number = defineModel("number");
const instituteId = defineModel("instituteId");
const authorId = defineModel("authorId");

const emitApply = (payload) => emit("apply", payload);
const emitClear = () => emit("clear");
</script>
<template>
    <div class="flex flex-wrap items-center gap-x-3 gap-y-3">
        <Select
            v-model="year"
            :options="options.years"
            placeholder="Rok"
            filter
            showClear
            class="w-36"
            @change="emitApply({ page: 1 })"
            :pt="{
                root: { class: '!rounded-xl' },
                label: { class: '!py-[0.6rem]' },
            }"
        />

        <Select
            v-model="number"
            :options="options.numbers"
            placeholder="Vydanie"
            filter
            showClear
            class="w-32"
            @change="emitApply({ page: 1 })"
            :pt="{
                root: { class: '!rounded-xl' },
                label: { class: '!py-[0.6rem]' },
            }"
        />

        <Select
            v-model="instituteId"
            :options="options.institutes"
            optionLabel="name"
            optionValue="id"
            placeholder="Inštitút"
            filter
            showClear
            class="w-56"
            @change="emitApply({ page: 1 })"
            :pt="{
                root: { class: '!rounded-xl' },
                label: { class: '!py-[0.6rem]' },
                overlay: {
                    style: 'width:min(470px, 74vw); max-width:min(470px, 74vw);',
                },
            }"
        />

        <Select
            v-model="authorId"
            :options="options.authors"
            optionLabel="label"
            optionValue="id"
            placeholder="Autor"
            filter
            showClear
            class="w-44"
            @change="emitApply({ page: 1 })"
            :pt="{
                root: { class: '!rounded-xl' },
                label: { class: '!py-[0.6rem]' },
            }"
        />

        <Button
            label="Zrušiť filter"
            severity="danger"
            @click="emitClear"
            class="!rounded-xl"
        />
    </div>
</template>
