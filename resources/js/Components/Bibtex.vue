<template>
    <Dialog v-model:visible="isVisible" modal header="Bibtex" :style="{ width: '50%' }" @hide="$emit('close')">
        <template #closebutton>
            <Button icon="pi pi-times" class="p-button-text" @click="$emit('close')" />
        </template>
        <div class="p-4">
            <div>@{{publication.type.toUpperCase()}} {,</div>
            <div>title = {{ publication.title }},</div>
            <div>author = {{ publication.author }},</div>
            <div>journal = {{ publication.journal }},</div>
            <div>year = {{ publication.year }},</div>
            <div>volume = {{ publication.volume }},</div>
            <div>number = {{ publication.number }},</div>
            <div>pages = {{ publication.pages }},</div>
            <div>issn = {{ publication.issn }},</div>
            <div>abstract = {{ publication.abstract }}</div>
            }
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
