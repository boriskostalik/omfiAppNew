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
    <div class="flex flex-wrap items-end gap-x-3 gap-y-4">
        <Select
            v-model="year"
            :options="options.years"
            placeholder="Rok"
            filter
            showClear
            class="w-28"
            @change="emitApply({ page: 1 })"
        />

        <Select
            v-model="number"
            :options="options.numbers"
            placeholder="Vydanie"
            filter
            showClear
            class="w-32"
            @change="emitApply({ page: 1 })"
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
                label: {
                    style: 'overflow:hidden; text-overflow:ellipsis; white-space:nowrap;',
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
        />

        <Button
            label="Zrušiť filtre"
            severity="danger"
            @click="emitClear"
            class="rounded-xl"
        />
    </div>
</template>
