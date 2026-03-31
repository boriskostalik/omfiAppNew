<script setup>
import { ref, watch } from "vue";
import {
    InputText,
    Button,
    Dialog,
    FileUpload,
    Message,
} from "primevue";
import { router } from "@inertiajs/vue3";

const props = defineProps({
    issue: { type: Object, default: null },
    visible: { type: Boolean, default: false },
});

const emit = defineEmits(["close"]);

const year = ref("");
const number = ref("");
const volume = ref("");
const pdf = ref(null);
const removePdf = ref(false);
const errors = ref({});
const loading = ref(false);

watch(
    () => props.visible,
    (val) => {
        if (val) {
            const i = props.issue;
            year.value = i ? String(i.year) : "";
            number.value = i ? String(i.number) : "";
            volume.value = i ? (i.volume ?? "") : "";
            pdf.value = null;
            removePdf.value = false;
            errors.value = {};
            loading.value = false;
        }
    },
);

const onFileSelect = (e) => {
    pdf.value = e.files[0];
};

const validate = () => {
    const errs = {};
    if (!year.value || !/^\d{4}$/.test(year.value)) {
        errs.year = "Rok musí byť 4-ciferné číslo";
    }
    if (!number.value || isNaN(Number(number.value))) {
        errs.number = "Číslo je povinné";
    }
    errors.value = errs;
    return Object.keys(errs).length === 0;
};

const submit = () => {
    if (!validate()) return;

    loading.value = true;

    const payload = {
        year: parseInt(year.value),
        number: parseInt(number.value),
        volume: volume.value || null,
        pdf: pdf.value || null,
    };

    if (props.issue) {
        router.post(
            route("issues.update", props.issue.id),
            {
                ...payload,
                _method: "PUT",
                remove_pdf: removePdf.value ? "true" : "false",
            },
            {
                forceFormData: true,
                onSuccess: () => emit("close"),
                onError: (e) => { errors.value = e; loading.value = false; },
            },
        );
    } else {
        router.post(route("issues.store"), payload, {
            forceFormData: true,
            onSuccess: () => emit("close"),
            onError: (e) => { errors.value = e; loading.value = false; },
        });
    }
};
</script>

<template>
    <Dialog
        :visible="props.visible"
        @update:visible="(val) => !val && $emit('close')"
        modal
        :header="issue ? 'Úprava vydania' : 'Pridanie vydania'"
        :style="{ width: '40vw', maxWidth: '540px' }"
        :breakpoints="{ '768px': '96vw' }"
    >
        <template #closebutton>
            <Button
                icon="pi pi-times"
                class="p-button-text p-button-sm"
                @click="$emit('close')"
            />
        </template>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-x-4 gap-y-3 mt-1">
            <div class="flex flex-col gap-0.5 col-span-1">
                <label class="text-xs font-medium text-gray-700">
                    Rok <span class="text-red-500">*</span>
                </label>
                <InputText
                    v-model="year"
                    class="w-full !text-sm !py-2"
                    maxlength="4"
                />
                <Message
                    v-if="errors.year"
                    severity="error"
                    size="small"
                    variant="simple"
                >
                    {{ errors.year }}
                </Message>
            </div>

            <div class="flex flex-col gap-0.5 col-span-1">
                <label class="text-xs font-medium text-gray-700">
                    Číslo <span class="text-red-500">*</span>
                </label>
                <InputText v-model="number" class="w-full !text-sm !py-2" />
                <Message
                    v-if="errors.number"
                    severity="error"
                    size="small"
                    variant="simple"
                >
                    {{ errors.number }}
                </Message>
            </div>

            <div class="flex flex-col gap-0.5 col-span-2">
                <label class="text-xs font-medium text-gray-700">Volume</label>
                <InputText v-model="volume" class="w-full !text-sm !py-2" />
            </div>

            <div class="flex flex-col gap-2 col-span-2 lg:col-span-4">
                <label class="text-xs font-medium text-gray-700">PDF</label>

                <div
                    v-if="issue?.pdf_path && !removePdf"
                    class="flex items-center justify-between bg-gray-50 border border-gray-200 rounded px-3 py-2"
                >
                    <a
                        :href="`/storage/${issue.pdf_path}`"
                        target="_blank"
                        class="text-sm text-[#1E4E8C] hover:underline flex items-center gap-2"
                    >
                        <i class="pi pi-file-pdf"></i>
                        Zobraziť PDF
                    </a>
                    <Button
                        icon="pi pi-trash"
                        label="Odstrániť"
                        severity="danger"
                        size="small"
                        text
                        @click="removePdf = true"
                    />
                </div>

                <FileUpload
                    v-if="!issue?.pdf_path || removePdf"
                    mode="basic"
                    accept=".pdf"
                    :maxFileSize="20000000"
                    chooseLabel="Vybrať PDF"
                    @select="onFileSelect"
                />
            </div>

            <div class="col-span-2 lg:col-span-4 flex items-center gap-3 mt-1">
                <Button
                    label="Zrušiť"
                    @click="$emit('close')"
                    text
                    class="w-full p-button-sm"
                />
                <Button
                    label="Uložiť"
                    @click="submit"
                    :loading="loading"
                    class="w-full p-button-sm !bg-primary !text-white"
                />
            </div>
        </div>
    </Dialog>
</template>
