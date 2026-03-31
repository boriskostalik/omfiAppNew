<script setup>
import { ref, watch, computed } from "vue";
import { InputText, Button, Dialog, Message, Select } from "primevue";
import { Form, FormField } from "@primevue/forms";
import { valibotResolver } from "@primevue/forms/resolvers/valibot";
import * as v from "valibot";
import { router } from "@inertiajs/vue3";

const props = defineProps({
    author: { type: Object, default: null },
    visible: { type: Boolean, default: false },
    institutes: { type: Array, default: () => [] },
});

const emit = defineEmits(["close"]);
const formKey = ref(0);
const instituteValue = ref("");
const customMode = ref(false);

watch(
    () => props.visible,
    (val) => {
        if (val) {
            formKey.value++;
            instituteValue.value = props.author?.institute?.name ?? "";
            customMode.value = false;
        }
    },
);

const initialValues = computed(() => {
    const a = props.author;
    if (a) {
        return {
            surname: a.surname ?? "",
            firstname: a.firstname ?? "",
            von: a.von ?? "",
            email: a.email ?? "",
            url: a.url ?? "",
        };
    }
    return {
        surname: "",
        firstname: "",
        von: "",
        email: "",
        url: "",
    };
});

const schema = v.object({
    surname: v.pipe(v.string(), v.minLength(1, "Priezvisko je povinné")),
    firstname: v.pipe(v.string(), v.minLength(1, "Meno je povinné")),
    von: v.nullish(v.string()),
    email: v.union([
        v.pipe(v.string(), v.email("Neplatný email")),
        v.literal(""),
    ]),
    url: v.nullish(v.string()),
});

const resolver = valibotResolver(schema);

const onSubmit = ({ valid, values }) => {
    if (!valid) return;

    const submitData = { ...values, institute: instituteValue.value || null };

    if (props.author) {
        router.put(`/dashboard/authors/${props.author.id}`, submitData, {
            onSuccess: () => emit("close"),
        });
    } else {
        router.post("/dashboard/authors", submitData, {
            onSuccess: () => emit("close"),
        });
    }
};
</script>

<template>
    <Dialog
        :visible="props.visible"
        @update:visible="(val) => !val && $emit('close')"
        modal
        :header="author ? 'Úprava autora' : 'Pridanie autora'"
        :style="{ width: '60vw', maxWidth: '800px' }"
        :breakpoints="{ '768px': '96vw' }"
    >
        <template #closebutton>
            <Button
                icon="pi pi-times"
                class="p-button-text p-button-sm"
                @click="$emit('close')"
            />
        </template>

        <Form
            :key="formKey"
            :resolver="resolver"
            :initialValues="initialValues"
            :validateOn="['submit']"
            @submit="onSubmit"
            class="grid grid-cols-2 lg:grid-cols-6 gap-x-4 gap-y-3"
        >
            <FormField
                v-slot="$field"
                name="surname"
                class="flex flex-col gap-0.5 col-span-2"
            >
                <label class="text-xs font-medium text-gray-700">
                    Priezvisko <span class="text-red-500">*</span>
                </label>
                <InputText v-bind="$field" class="w-full !text-sm !py-2" />
                <Message
                    v-if="$field.invalid"
                    severity="error"
                    size="small"
                    variant="simple"
                >
                    {{ $field.error?.message }}
                </Message>
            </FormField>

            <FormField
                v-slot="$field"
                name="firstname"
                class="flex flex-col gap-0.5 col-span-2"
            >
                <label class="text-xs font-medium text-gray-700">
                    Meno <span class="text-red-500">*</span>
                </label>
                <InputText v-bind="$field" class="w-full !text-sm !py-2" />
                <Message
                    v-if="$field.invalid"
                    severity="error"
                    size="small"
                    variant="simple"
                >
                    {{ $field.error?.message }}
                </Message>
            </FormField>

            <FormField
                v-slot="$field"
                name="von"
                class="flex flex-col gap-0.5 col-span-2"
            >
                <label class="text-xs font-medium text-gray-700">Von</label>
                <InputText v-bind="$field" class="w-full !text-sm !py-2" />
            </FormField>

            <FormField
                v-slot="$field"
                name="email"
                class="flex flex-col gap-0.5 col-span-2 lg:col-span-3"
            >
                <label class="text-xs font-medium text-gray-700">Email</label>
                <InputText
                    v-bind="$field"
                    type="email"
                    class="w-full !text-sm !py-2"
                />
                <Message
                    v-if="$field.invalid"
                    severity="error"
                    size="small"
                    variant="simple"
                >
                    {{ $field.error?.message }}
                </Message>
            </FormField>

            <FormField
                v-slot="$field"
                name="url"
                class="flex flex-col gap-0.5 col-span-2 lg:col-span-3"
            >
                <label class="text-xs font-medium text-gray-700">URL</label>
                <InputText v-bind="$field" class="w-full !text-sm !py-2" />
            </FormField>

            <div class="flex flex-col gap-0.5 col-span-2 lg:col-span-6">
                <div class="flex items-center justify-between">
                    <label class="text-xs font-medium text-gray-700"
                        >Inštitúcia</label
                    >
                    <button
                        type="button"
                        class="text-xs text-[#1E4E8C] hover:underline"
                        @click="
                            customMode = !customMode;
                            instituteValue = '';
                        "
                    >
                        {{
                            customMode
                                ? "Vybrať zo zoznamu"
                                : "Pridať inštitúciu"
                        }}
                    </button>
                </div>
                <Select
                    v-if="!customMode"
                    v-model="instituteValue"
                    :options="institutes.map((i) => i.name)"
                    showClear
                    filter
                    placeholder="Vyberte inštitúciu"
                    size="small"
                    class="w-full"
                />
                <InputText
                    v-else
                    v-model="instituteValue"
                    placeholder="Napíšte názov inštitúcie"
                    class="w-full !text-sm !py-2"
                />
            </div>

            <div class="col-span-2 lg:col-span-6 flex items-center gap-3 mt-1">
                <Button
                    label="Zrušiť"
                    @click="$emit('close')"
                    text
                    class="w-full p-button-sm"
                />
                <Button
                    type="submit"
                    label="Uložiť"
                    class="w-full p-button-sm !bg-primary !text-white"
                />
            </div>
        </Form>
    </Dialog>
</template>
