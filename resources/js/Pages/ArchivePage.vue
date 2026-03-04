<script setup>
import { computed } from "vue";
import PanelMenu from "primevue/panelmenu";
import { router } from "@inertiajs/vue3";

const props = defineProps({
    years: Array,
    issuesByYear: Object,
});

const goIssue = (id) => router.get(route("archive.issue", id));
const issueLabel = (y, iss) => {
    const num = iss?.number;
    return num !== null && num !== undefined && num !== "" && num !== 0
        ? `${y}/${num}`
        : `${y}/—`;
};
const rowCells = (y) => props.issuesByYear?.[y] ?? [];
const items = computed(() =>
    (props.years ?? []).map((y) => ({
        key: String(y),
        label: String(y),
        items: (props.issuesByYear?.[y] ?? []).map((iss) => ({
            key: `i-${iss.id}`,
            label: issueLabel(y, iss),
            id: iss.id,
            volume: iss.volume,
        })),
    })),
);
</script>

<template>
    <div class="max-w-4xl mx-auto px-4 py-10">
        <div class="mb-12 border-b border-gray-200 pb-8">
            <h1 class="text-4xl sm:text-5xl font-bold text-gray-900 mb-3">
                Archív
            </h1>
        </div>
        <div class="md:hidden archive-panelmenu">
            <PanelMenu :model="items" :multiple="true">
                <template #item="{ item }">
                    <div v-if="item.items" class="w-full">
                        <div
                            class="w-full bg-[#1E4E8C] text-white font-bold flex items-center justify-between px-5 h-14"
                        >
                            <span class="text-lg tracking-wide">{{
                                item.label
                            }}</span>
                            <span
                                class="text-xs font-normal opacity-60 uppercase tracking-widest"
                                >Ročník</span
                            >
                        </div>
                    </div>
                    <button
                        v-else
                        type="button"
                        @click="goIssue(item.id)"
                        class="w-full bg-white hover:bg-slate-50 transition flex items-center justify-between px-5 border-t border-slate-100"
                        style="height: 56px"
                    >
                        <span class="text-base font-semibold text-gray-800">{{
                            item.label
                        }}</span>
                        <span v-if="item.volume" class="text-xs text-slate-400"
                            >Vol. {{ item.volume }}</span
                        >
                    </button>
                </template>
            </PanelMenu>
        </div>
        <div class="hidden md:block space-y-px">
            <div
                v-for="y in props.years"
                :key="`d-${y}`"
                class="flex items-stretch"
            >
                <div
                    class="bg-[#1E4E8C] text-white font-bold flex items-center justify-center shrink-0"
                    style="width: 110px; min-height: 70px"
                >
                    <span class="text-xl tracking-wide">{{ y }}</span>
                </div>
                <div
                    class="flex-1 border-t border-b border-r border-gray-200 overflow-x-auto"
                >
                    <div class="inline-flex h-full">
                        <button
                            v-for="cell in rowCells(y)"
                            :key="cell.id"
                            type="button"
                            @click="goIssue(cell.id)"
                            class="border-r border-gray-200 last:border-r-0 bg-white hover:bg-slate-50 transition px-8 flex flex-col items-center justify-center text-gray-800 gap-0.5"
                            style="min-width: 150px; min-height: 70px"
                        >
                            <span class="text-base font-semibold">{{
                                issueLabel(y, cell)
                            }}</span>
                            <span
                                v-if="cell.volume"
                                class="text-xs text-gray-400"
                                >Vol. {{ cell.volume }}</span
                            >
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
