<script setup>
import { ref, watch, computed } from "vue";
import {
    InputText,
    InputChips,
    Textarea,
    Select,
    Button,
    Dialog,
    MultiSelect,
    Message,
} from "primevue";
import { Form, FormField } from "@primevue/forms";
import { valibotResolver } from "@primevue/forms/resolvers/valibot";
import * as v from "valibot";
import { router } from "@inertiajs/vue3";

const props = defineProps({
    publication: { type: Object },
    authors: { type: Array, default: () => [] },
    issues: { type: Array, default: () => [] },
    visible: { type: Boolean, default: false },
});
const emit = defineEmits(["close"]);

const serverErrors = ref({});
const localBibtexId = ref("");
const formKey = ref(0);

watch(
    () => props.visible,
    (val) => {
        if (val) formKey.value++;
    },
);

const issueOptions = computed(() =>
    props.issues.map((iss) => ({
        id: iss.id,
        label: iss.volume
            ? `${iss.year}/${iss.number} (Vol. ${iss.volume})`
            : `${iss.year}/${iss.number}`,
    })),
);

const initialValues = computed(() => {
    const pub = props.publication;
    if (pub) {
        return {
            type: pub.type ?? null,
            issue_id: pub.issue_id ?? null,
            actualyear: pub.actualyear ?? "",
            month: pub.month ?? null,
            edition: pub.edition ?? "",
            chapter: pub.chapter ?? "",
            title: pub.title ?? "",
            title_eng: pub.title_eng ?? "",
            authors:
                pub.authors
                    ?.filter((a) => a.pivot?.is_editor !== "Y")
                    .map((a) => ({ id: a.id, cleanname: a.cleanname })) ?? [],
            editors:
                pub.authors
                    ?.filter((a) => a.pivot?.is_editor === "Y")
                    .map((a) => ({ id: a.id, cleanname: a.cleanname })) ?? [],
            journal: pub.journal ?? "",
            firstpage: pub.firstpage ?? "",
            lastpage: pub.lastpage ?? "",
            doi: pub.doi ?? "",
            issn: pub.issn ?? "1335-4981",
            isbn: pub.isbn ?? "",
            keywords: pub.keywords ? pub.keywords.split(",").map((k) => k.trim()).filter(Boolean) : [],
            abstract: pub.abstract ?? "",
        };
    }
    return {
        type: null,
        issue_id: null,
        actualyear: "",
        month: null,
        edition: "",
        chapter: "",
        title: "",
        title_eng: "",
        authors: [],
        editors: [],
        journal: "Obzory matematiky, fyziky a informatiky",
        firstpage: "",
        lastpage: "",
        doi: "",
        issn: "1335-4981",
        isbn: "",
        keywords: [],
        abstract: "",
    };
});

const schema = v.object({
    title: v.pipe(v.string(), v.minLength(1, "Názov je povinný")),
    authors: v.pipe(
        v.array(v.any()),
        v.check(
            (arr) => Array.isArray(arr) && arr.length > 0,
            "Aspoň jeden autor je povinný",
        ),
    ),
    type: v.pipe(
        v.union([v.string(), v.null()]),
        v.check(
            (val) => typeof val === "string" && val.length > 0,
            "Typ publikácie je povinný",
        ),
    ),
    issue_id: v.pipe(
        v.union([v.number(), v.null()]),
        v.check((val) => val !== null, "Vydanie je povinné"),
    ),
    actualyear: v.nullish(v.string()),
    title_eng: v.nullish(v.string()),
    editors: v.optional(v.array(v.any())),
    journal: v.nullish(v.string()),
    firstpage: v.pipe(v.string(), v.minLength(1, "Prvá strana je povinná")),
    lastpage: v.pipe(v.string(), v.minLength(1, "Posledná strana je povinná")),
    doi: v.nullish(v.string()),
    issn: v.pipe(v.string(), v.minLength(1, "ISSN je povinné")),
    isbn: v.nullish(v.string()),
    keywords: v.optional(v.array(v.string())),
    abstract: v.nullish(v.string()),
});

const resolver = valibotResolver(schema);

const types = [
    "Article",
    "Book",
    "Booklet",
    "Inbook",
    "Incollection",
    "Inproceedings",
    "Manual",
    "Mastersthesis",
    "Misc",
    "Phdthesis",
    "Proceedings",
    "Techreport",
    "Unpublished",
];

const mappedAuthors = computed(() =>
    props.authors.map((author) => ({
        id: author.id,
        cleanname: author.cleanname,
    })),
);

const numericOnly = (e) => {
    if (!/[0-9]/.test(e.key)) e.preventDefault();
};

const onSubmit = ({ valid, values }) => {
    if (!valid) return;
    serverErrors.value = {};

    const editors = values.editors ?? [];
    const editorIds = new Set(editors.map((e) => e.id));
    const authorIds = new Set(values.authors.map((a) => a.id));

    const mergedAuthors = [
        ...values.authors.map((a) => ({
            id: a.id,
            is_editor: editorIds.has(a.id) ? "Y" : "N",
        })),
        ...editors
            .filter((e) => !authorIds.has(e.id))
            .map((e) => ({ id: e.id, is_editor: "Y" })),
    ];

    const submitData = {
        ...values,
        bibtex_id: localBibtexId.value,
        keywords: (values.keywords ?? []).join(", "),
        authors: mergedAuthors,
    };
    delete submitData.editors;

    if (props.publication) {
        router.put(
            `/dashboard/publications/${props.publication.id}`,
            submitData,
            {
                onError: (err) => {
                    serverErrors.value = err;
                },
                onSuccess: () => emit("close"),
            },
        );
    } else {
        router.post("/dashboard/publications", submitData, {
            onError: (err) => {
                serverErrors.value = err;
            },
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
        :header="publication ? 'Úprava publikácie' : 'Pridanie publikácie'"
        :style="{ width: '92vw', maxWidth: '1200px' }"
        :breakpoints="{ '1024px': '96vw', '640px': '100vw' }"
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
                name="type"
                class="flex flex-col gap-0.5 lg:col-span-2"
            >
                <label class="text-xs font-medium text-gray-700"
                    >Typ publikácie <span class="text-red-500">*</span></label
                >
                <Select
                    v-bind="$field"
                    :options="types"
                    placeholder="Vyberte typ"
                    showClear
                    size="small"
                    class="w-full"
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
                name="issue_id"
                class="flex flex-col gap-0.5 lg:col-span-2"
            >
                <label class="text-xs font-medium text-gray-700"
                    >Vydanie <span class="text-red-500">*</span>
                    <span class="text-gray-400 font-normal text-[11px]"
                        >(rok/číslo)</span
                    ></label
                >
                <Select
                    v-bind="$field"
                    :options="issueOptions"
                    optionLabel="label"
                    optionValue="id"
                    placeholder="Vyberte vydanie"
                    showClear
                    size="small"
                    class="w-full"
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
                name="journal"
                class="flex flex-col gap-0.5 lg:col-span-2"
            >
                <label class="text-xs font-medium text-gray-700">Journal</label>
                <InputText v-bind="$field" class="w-full !text-sm !py-2" />
            </FormField>

            <FormField
                v-slot="$field"
                name="title"
                class="flex flex-col gap-0.5 col-span-2 lg:col-span-3"
            >
                <label class="text-xs font-medium text-gray-700"
                    >Názov <span class="text-red-500">*</span></label
                >
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
                name="title_eng"
                class="flex flex-col gap-0.5 col-span-2 lg:col-span-3"
            >
                <label class="text-xs font-medium text-gray-700"
                    >Názov (anglicky)</label
                >
                <InputText v-bind="$field" class="w-full !text-sm !py-2" />
            </FormField>

            <FormField
                v-slot="$field"
                name="actualyear"
                class="flex flex-col gap-0.5 lg:col-span-1"
            >
                <label class="text-xs font-medium text-gray-700">Rok</label>
                <InputText
                    v-bind="$field"
                    class="w-full !text-sm !py-2"
                    @keypress="numericOnly"
                />
            </FormField>

            <FormField
                v-slot="$field"
                name="firstpage"
                class="flex flex-col gap-0.5 lg:col-span-1"
            >
                <label class="text-xs font-medium text-gray-700"
                    >Strana od <span class="text-red-500">*</span></label
                >
                <InputText
                    v-bind="$field"
                    class="w-full !text-sm !py-2"
                    @keypress="numericOnly"
                />
                <Message
                    v-if="$field.invalid"
                    severity="error"
                    size="small"
                    variant="simple"
                    >{{ $field.error?.message }}</Message
                >
            </FormField>

            <FormField
                v-slot="$field"
                name="lastpage"
                class="flex flex-col gap-0.5 lg:col-span-1"
            >
                <label class="text-xs font-medium text-gray-700"
                    >Strana do <span class="text-red-500">*</span></label
                >
                <InputText
                    v-bind="$field"
                    class="w-full !text-sm !py-2"
                    @keypress="numericOnly"
                />
                <Message
                    v-if="$field.invalid"
                    severity="error"
                    size="small"
                    variant="simple"
                    >{{ $field.error?.message }}</Message
                >
            </FormField>

            <FormField
                v-slot="$field"
                name="doi"
                class="flex flex-col gap-0.5 lg:col-span-1"
            >
                <label class="text-xs font-medium text-gray-700">DOI</label>
                <InputText v-bind="$field" class="w-full !text-sm !py-2" />
            </FormField>

            <FormField
                v-slot="$field"
                name="issn"
                class="flex flex-col gap-0.5 lg:col-span-1"
            >
                <label class="text-xs font-medium text-gray-700"
                    >ISSN <span class="text-red-500">*</span></label
                >
                <InputText v-bind="$field" class="w-full !text-sm !py-2" />
                <Message
                    v-if="$field.invalid"
                    severity="error"
                    size="small"
                    variant="simple"
                    >{{ $field.error?.message }}</Message
                >
            </FormField>

            <FormField
                v-slot="$field"
                name="isbn"
                class="flex flex-col gap-0.5 lg:col-span-1"
            >
                <label class="text-xs font-medium text-gray-700">ISBN</label>
                <InputText v-bind="$field" class="w-full !text-sm !py-2" />
            </FormField>

            <FormField
                v-slot="$field"
                name="authors"
                class="flex flex-col gap-0.5 col-span-2 lg:col-span-3"
            >
                <label class="text-xs font-medium text-gray-700"
                    >Autori <span class="text-red-500">*</span></label
                >
                <MultiSelect
                    v-bind="$field"
                    :options="mappedAuthors"
                    optionLabel="cleanname"
                    filter
                    placeholder="Vyberte autorov"
                    display="chip"
                    :maxSelectedLabels="4"
                    size="small"
                    class="w-full"
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
                name="editors"
                class="flex flex-col gap-0.5 col-span-2 lg:col-span-3"
            >
                <label class="text-xs font-medium text-gray-700">Editori</label>
                <MultiSelect
                    v-bind="$field"
                    :options="mappedAuthors"
                    optionLabel="cleanname"
                    filter
                    placeholder="Vyberte editorov"
                    display="chip"
                    :maxSelectedLabels="4"
                    size="small"
                    class="w-full"
                />
            </FormField>

            <FormField
                v-slot="$field"
                name="keywords"
                class="flex flex-col gap-0.5 col-span-2 lg:col-span-6"
            >
                <label class="text-xs font-medium text-gray-700">Kľúčové slová</label>
                <InputChips
                    :modelValue="$field.value"
                    @update:modelValue="$field.onInput({ target: { value: $event } })"
                    class="w-full"
                />
            </FormField>

            <FormField
                v-slot="$field"
                name="abstract"
                class="flex flex-col gap-0.5 col-span-2 lg:col-span-6"
            >
                <label class="text-xs font-medium text-gray-700"
                    >Abstrakt</label
                >
                <Textarea v-bind="$field" rows="3" class="w-full !text-sm" />
            </FormField>

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
