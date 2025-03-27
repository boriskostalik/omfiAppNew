<template>
    <Dialog v-model:visible="isVisible" modal header="RIS Format" :style="{ width: '50%' }" @hide="$emit('close')">
        <template #closebutton>
            <Button icon="pi pi-times" class="p-button-text" @click="$emit('close')" />
        </template>
        <div class="p-4">
            <div>TY - {{ publication.type.toUpperCase() }}</div>
            <div>T1 - {{ publication.title }}</div>
            <div>A1 - {{ publication.author }}</div>
            <div>JA - {{ publication.journal }}</div>
            <div>Y1 - {{ publication.year }}</div>
            <div>VL - {{ publication.volume }}</div>
            <div>IS - {{ publication.number }}</div>
            <div>SP - {{ publication.firstpage }}</div>
            <div>EP - {{ publication.lastpage }}</div>
            <div>SN - {{ publication.issn }}</div>
            <div>N2 - {{ publication.abstract }}</div>
            <div>ER -</div>
        </div>
    </Dialog>
</template>

<script setup>
import { computed } from 'vue';
import { Dialog, Button } from 'primevue';

const props = defineProps({
    visible: {
        type: Boolean,
        default: false
    },
    publication: {
        type: Object,
        default: null
    }
});

const isVisible = computed(() => props.visible);
</script>

<style scoped>
.publication {
  border: 1px solid #ccc;
  padding: 16px;
  border-radius: 8px;
  max-width: 600px;
  margin: 20px auto;
  background-color: #f9f9f9;
  font-family: monospace;
  white-space: pre-wrap;
}
</style>