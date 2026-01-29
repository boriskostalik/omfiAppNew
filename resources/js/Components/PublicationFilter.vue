<template>
  <div class="mt-4 mb-4">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
      <div class="lg:col-span-1">
        <Select
          v-model="year"
          :options="options.years"
          placeholder="Rok"
          filter
          showClear
          class="w-full"
          @change="emitApply({ page: 1 })"
        />
      </div>

      <div class="lg:col-span-1">
        <Select
          v-model="number"
          :options="options.numbers"
          placeholder="Vydanie"
          filter
          showClear
          class="w-full"
          @change="emitApply({ page: 1 })"
        />
      </div>

      <div class="lg:col-span-1">
        <Select
            v-model="institute"
            :options="options.institutes"
            placeholder="Inštitút"
            filter
            showClear
            class="w-full"
            appendTo="self"
            @change="emitApply({ page: 1 })"
            :pt="{
                overlay: { style: 'width:min(300px, calc(100vw - 2rem)); max-width:min(300px, calc(100vw - 2rem));' },
                listContainer: { style: 'overflow-x:auto; overflow-y:auto; -webkit-overflow-scrolling:touch; touch-action:pan-x pan-y;' },
                list: { style: 'width:max-content; min-width:100%;' },
                option: { style: 'min-width:max-content;' },
                optionLabel: { style: 'white-space:nowrap; display:inline-block;' },
                label: { style: 'overflow:hidden; text-overflow:ellipsis; white-space:nowrap;' }
            }"
            />

      </div>

      <div class="lg:col-span-1">
        <Select
          v-model="authorId"
          :options="options.authors"
          optionLabel="label"
          optionValue="id"
          placeholder="Autor"
          filter
          showClear
          class="w-full"
          @change="emitApply({ page: 1 })"
        />
      </div>

      <div class="lg:col-span-1 flex gap-2">
        <Button label="Zrušiť filtre" class="w-full" @click="emitClear" />
      </div>
    </div>
  </div>
</template>

<script setup>
import Button from 'primevue/button'
import Select from 'primevue/select'

defineProps({
  options: {
    type: Object,
    required: true,
  },
})

const emit = defineEmits(['apply', 'clear'])

const year = defineModel('year')
const number = defineModel('number')
const institute = defineModel('institute')
const authorId = defineModel('authorId')

function emitApply(payload) {
  emit('apply', payload)
}

function emitClear() {
  emit('clear')
}
</script>
