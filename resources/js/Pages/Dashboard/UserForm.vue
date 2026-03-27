<script setup>
import { ref, watch, computed } from "vue";
import { InputText, Button, Dialog, Select, Message } from "primevue";
import { Form, FormField } from "@primevue/forms";
import { valibotResolver } from "@primevue/forms/resolvers/valibot";
import * as v from "valibot";
import { router } from "@inertiajs/vue3";

const props = defineProps({
    user: { type: Object, default: null },
    visible: { type: Boolean, default: false },
});

const emit = defineEmits(["close"]);
const formKey = ref(0);
const passwordMatchError = ref("");
const serverErrors = ref({});

watch(
    () => props.visible,
    (val) => {
        if (val) {
            formKey.value++;
            passwordMatchError.value = "";
            serverErrors.value = {};
        }
    }
);

const initialValues = computed(() => {
    if (props.user) {
        return {
            name: props.user.name ?? "",
            email: props.user.email ?? "",
            role: props.user.role ?? "",
        };
    }
    return { name: "", email: "", role: "", password: "", passwordCheck: "" };
});

const roleOptions = ["admin", "editor"];

const schema = computed(() => {
    const base = {
        name: v.pipe(v.string(), v.minLength(1, "Meno je povinné")),
        email: v.pipe(v.string(), v.email("Neplatný email")),
        role: v.pipe(v.string(), v.minLength(1, "Rola je povinná")),
    };
    if (props.user) return v.object(base);
    return v.object({
        ...base,
        password: v.pipe(v.string(), v.minLength(8, "Heslo musí mať aspoň 8 znakov")),
        passwordCheck: v.pipe(v.string(), v.minLength(1, "Potvrďte heslo")),
    });
});

const resolver = computed(() => valibotResolver(schema.value));

const onSubmit = ({ valid, values }) => {
    if (!valid) return;
    if (!props.user && values.password !== values.passwordCheck) {
        passwordMatchError.value = "Heslá sa nezhodujú";
        return;
    }
    passwordMatchError.value = "";
    const payload = { ...values };
    delete payload.passwordCheck;

    if (props.user) {
        router.put(route("users.update", props.user.id), payload, {
            onSuccess: () => emit("close"),
            onError: (err) => { serverErrors.value = err; },
        });
    } else {
        router.post(route("users.store"), payload, {
            onSuccess: () => emit("close"),
            onError: (err) => { serverErrors.value = err; },
        });
    }
};
</script>

<template>
    <Dialog
        :visible="props.visible"
        @update:visible="(val) => !val && $emit('close')"
        modal
        :header="user ? 'Úprava používateľa' : 'Pridanie používateľa'"
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

        <Form
            :key="formKey"
            :resolver="resolver"
            :initialValues="initialValues"
            :validateOn="['submit']"
            @submit="onSubmit"
            class="grid grid-cols-2 gap-x-4 gap-y-3 mt-1"
        >
            <FormField v-slot="$field" name="name" class="flex flex-col gap-0.5 col-span-2">
                <label class="text-xs font-medium text-gray-700">
                    Meno <span class="text-red-500">*</span>
                </label>
                <InputText v-bind="$field" class="w-full !text-sm !py-2" />
                <Message v-if="$field.invalid" severity="error" size="small" variant="simple">
                    {{ $field.error?.message }}
                </Message>
            </FormField>

            <FormField v-slot="$field" name="email" class="flex flex-col gap-0.5 col-span-2">
                <label class="text-xs font-medium text-gray-700">
                    Email <span class="text-red-500">*</span>
                </label>
                <InputText v-bind="$field" type="email" class="w-full !text-sm !py-2" />
                <Message v-if="$field.invalid" severity="error" size="small" variant="simple">
                    {{ $field.error?.message }}
                </Message>
                <Message v-if="serverErrors.email" severity="error" size="small" variant="simple">
                    {{ serverErrors.email }}
                </Message>
            </FormField>

            <FormField v-slot="$field" name="role" class="flex flex-col gap-0.5 col-span-2">
                <label class="text-xs font-medium text-gray-700">
                    Rola <span class="text-red-500">*</span>
                </label>
                <Select
                    v-bind="$field"
                    :options="roleOptions"
                    placeholder="Vybrať rolu"
                    class="w-full !text-sm"
                />
                <Message v-if="$field.invalid" severity="error" size="small" variant="simple">
                    {{ $field.error?.message }}
                </Message>
            </FormField>

            <template v-if="!user">
                <FormField v-slot="$field" name="password" class="flex flex-col gap-0.5 col-span-2">
                    <label class="text-xs font-medium text-gray-700">
                        Heslo <span class="text-red-500">*</span>
                    </label>
                    <InputText v-bind="$field" type="password" class="w-full !text-sm !py-2" />
                    <Message v-if="$field.invalid" severity="error" size="small" variant="simple">
                        {{ $field.error?.message }}
                    </Message>
                </FormField>

                <FormField v-slot="$field" name="passwordCheck" class="flex flex-col gap-0.5 col-span-2">
                    <label class="text-xs font-medium text-gray-700">
                        Potvrdiť heslo <span class="text-red-500">*</span>
                    </label>
                    <InputText v-bind="$field" type="password" class="w-full !text-sm !py-2" />
                    <Message v-if="$field.invalid" severity="error" size="small" variant="simple">
                        {{ $field.error?.message }}
                    </Message>
                    <Message v-if="passwordMatchError" severity="error" size="small" variant="simple">
                        {{ passwordMatchError }}
                    </Message>
                </FormField>
            </template>

            <div class="col-span-2 flex items-center gap-3 mt-1">
                <Button label="Zrušiť" @click="$emit('close')" text class="w-full p-button-sm" />
                <Button type="submit" label="Uložiť" class="w-full p-button-sm !bg-primary !text-white" />
            </div>
        </Form>
    </Dialog>
</template>
