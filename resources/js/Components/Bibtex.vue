<script setup>
import { computed } from "vue";
import Dialog from "primevue/dialog";
import Button from "primevue/button";

const props = defineProps({
    visible: { type: Boolean, default: false },
    publication: { type: Object, default: null },
});

const emit = defineEmits(["close", "update:visible"]);

const isVisible = computed({
    get: () => props.visible,
    set: (val) => emit("update:visible", val),
});

const handleClose = () => {
    isVisible.value = false;
    emit("close");
};

const getAuthorsCleanNames = () => {
    const authors = props.publication?.authors ?? [];
    return authors
        .map((a) => a?.cleanname)
        .filter(Boolean)
        .join(" and ");
};

const normalizeBibtexType = (t) => {
    const type = (t ?? "article").toString().toLowerCase();
    return type;
};

const generateCiteKey = () => {
    const pub = props.publication ?? {};
    const year = pub?.issue?.year ?? pub?.year ?? "n.d.";
    const firstAuthor = (pub?.authors?.[0]?.cleanname ?? "unknown")
        .split(" ")
        .filter(Boolean)
        .slice(-1)[0]
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, "");
    return `${firstAuthor}${year}`;
};

const fieldLine = (key, value, { trailingComma = true } = {}) => {
    const v = (value ?? "").toString().trim();
    if (!v) return null;
    return `  ${key} = {${v}}${trailingComma ? "," : ""}`;
};

const JOURNAL_NAME = "Obzory matematiky, fyziky a informatiky";
const JOURNAL_ISSN = "1335-4981";

const bibtexText = computed(() => {
    const pub = props.publication;
    if (!pub) return "";

    const type = normalizeBibtexType(pub.type);
    const citeKey = pub.cite_key || pub.citeKey || generateCiteKey();

    const lines = [
        `@${type}{${citeKey},`,
        fieldLine("title", pub.title),
        fieldLine("author", getAuthorsCleanNames()),
        fieldLine("journal", JOURNAL_NAME),
        fieldLine("year", pub.issue?.year),
        fieldLine("volume", pub.issue?.volume),
        fieldLine("number", pub.issue?.number),
        fieldLine("pages", pub.firstpage && pub.lastpage ? `${pub.firstpage}--${pub.lastpage}` : (pub.firstpage || null)),
        fieldLine("issn", JOURNAL_ISSN),
        fieldLine("abstract", pub.abstract, { trailingComma: false }),
    ].filter(Boolean);

    lines.push("}");
    return lines.join("\n");
});

const copyToClipboard = async () => {
    try {
        await navigator.clipboard.writeText(bibtexText.value);
    } catch (e) {
        const ta = document.createElement("textarea");
        ta.value = bibtexText.value;
        document.body.appendChild(ta);
        ta.select();
        document.execCommand("copy");
        document.body.removeChild(ta);
    }
};
</script>

<template>
    <Dialog
        v-model:visible="isVisible"
        modal
        header="BibTeX"
        :style="{ width: '50%' }"
        :breakpoints="{ '768px': '96vw' }"
        @hide="handleClose"
    >
        <template #closebutton>
            <Button
                icon="pi pi-times"
                class="p-button-text"
                @click="handleClose"
            />
        </template>

        <div class="p-4">
            <div class="flex justify-end mb-3">
                <Button
                    icon="pi pi-copy"
                    label="Copy"
                    class="p-button-sm p-button-outlined"
                    @click="copyToClipboard"
                />
            </div>

            <pre
                class="border border-gray-300 p-4 rounded-lg bg-gray-50 font-mono whitespace-pre-wrap m-0"
                >{{ bibtexText }}</pre
            >
        </div>
    </Dialog>
</template>
