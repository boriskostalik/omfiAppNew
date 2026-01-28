<template>
  <Dropdown
    v-model="sortKey"
    :options="options"
    optionLabel="label"
    optionValue="value"
    placeholder="Zoradiť"
    class="w-48"
    @change="emit('change', { page: 1 })"
  />
</template>

<script setup>
import Dropdown from 'primevue/dropdown'

const emit = defineEmits(['change'])
const sortKey = defineModel('sortKey')

const options = [
  { label: 'Názov A → Z', value: 'title_asc' },
  { label: 'Názov Z → A', value: 'title_desc' },
  { label: 'Rok ↑', value: 'year_asc' },
  { label: 'Rok ↓', value: 'year_desc' },
]
</script>
