<template>
  <div class="w-full max-w-[1000px] mx-auto px-4 mt-16">
    <PublicationDetailCard
      :publication="publication"
      :pdf-url="pdfUrl"
      :pdf-viewer-src="pdfViewerSrc"
      @show-bibtex="isBibtexVisible = true"
      @show-ris="isRISVisible = true"
    />

    <Card class="mt-6">
      <template #title>
        Autori
      </template>

      <template #content>
        <div v-if="publication.authors?.length" class="mt-2">
          <div v-for="author in publication.authors" :key="author.id">
            <Author :author="author" />
          </div>
        </div>

        <div v-else class="text-gray-500 italic">
          Žiadni autori.
        </div>
      </template>
    </Card>

    <!-- Modaly -->
    <Bibtex :publication="publication" :visible="isBibtexVisible" @close="isBibtexVisible = false" />
    <RIS :publication="publication" :visible="isRISVisible" @close="isRISVisible = false" />
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'

import Card from 'primevue/card'

import Author from '@/Components/Author.vue'
import Bibtex from '@/Components/Bibtex.vue'
import RIS from '@/Components/RIS.vue'
import PublicationDetailCard from '@/Components/PublicationDetailCard.vue'

const props = defineProps({
  publication: { type: Object, required: true },
})

const isBibtexVisible = ref(false)
const isRISVisible = ref(false)

/**
 * 🔧 Uprav podľa backendu:
 * ideálne: publication.pdf_url (plná URL)
 */
const pdfUrl = computed(() => {
  return (
    props.publication?.pdf_url ||
    props.publication?.pdfUrl ||
    props.publication?.pdf ||
    props.publication?.file_url ||
    null
  )
})

const pdfViewerSrc = computed(() => {
  if (!pdfUrl.value) return null
  return `${pdfUrl.value}#page=1&view=FitH`
})
</script>
