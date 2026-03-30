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

const risType = computed(() => {
    const t = (props.publication?.type ?? "").toString().toUpperCase();
    if (t === "ARTICLE" || t === "JOUR" || t === "JOURNAL") return "JOUR";
    return "JOUR";
});

const authorsLines = computed(() => {
    const pub = props.publication;
    const authors = pub?.authors ?? [];
    const list = authors
        .map((a) => a?.cleanname || a?.name || a?.fullname)
        .filter(Boolean);

    if (list.length) return list.map((name) => `A1  - ${name}`).join("\n");

    const single = (pub?.author ?? "").toString().trim();
    if (single) return `A1  - ${single}`;

    return "";
});

const line = (tag, value) => {
    const v = (value ?? "").toString().trim();
    if (!v) return null;
    return `${tag}  - ${v}`;
};

const JOURNAL_NAME = "Obzory matematiky, fyziky a informatiky";
const JOURNAL_ISSN = "1335-4981";

const risText = computed(() => {
    const pub = props.publication;
    if (!pub) return "";

    const lines = [
        `TY  - ${risType.value}`,
        line("T1", pub.title),
        authorsLines.value || null,
        line("JA", JOURNAL_NAME),
        line("Y1", pub.issue?.year),
        line("VL", pub.issue?.volume),
        line("IS", pub.issue?.number),
        line("SP", pub.firstpage ?? pub.sp ?? pub.start_page),
        line("EP", pub.lastpage ?? pub.ep ?? pub.end_page),
        line("SN", JOURNAL_ISSN),
        line("N2", pub.abstract),
        "ER  -",
    ].filter(Boolean);

    return lines.join("\n");
});

const copyToClipboard = async () => {
    try {
        await navigator.clipboard.writeText(risText.value);
    } catch (e) {
        const ta = document.createElement("textarea");
        ta.value = risText.value;
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
        header="RIS Format"
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
                >{{ risText }}</pre
            >
        </div>
    </Dialog>
</template>
