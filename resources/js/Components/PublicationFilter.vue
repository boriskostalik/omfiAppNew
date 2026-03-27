<script setup>
import Select from "primevue/select";
import Button from "primevue/button";

defineProps({
    options: { type: Object, required: true },
});

const emit = defineEmits(["apply", "clear"]);
const year = defineModel("year");
const number = defineModel("number");
const institute = defineModel("institute");
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
            v-model="institute"
            :options="options.institutes"
            placeholder="Inštitút"
            filter
            showClear
            class="w-52"
            appendTo="self"
            @change="emitApply({ page: 1 })"
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
