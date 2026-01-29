<template>
  <div class="w-full max-w-[1000px] mx-auto px-4 mt-16">
    <Card>
      <template #title>
        <div class="text-4xl font-semibold text-slate-900">
                    {{ author.cleanname.replace(',', '') }}
        </div>
        <Divider />
      </template>

      <template #content>
        <div class="space-y-4 text-base text-slate-700">
          <div v-if="author.email" class="flex gap-2">
            <strong class="text-slate-900">Email:</strong>
            <a
              :href="'mailto:' + author.email"
              class="text-blue-600 hover:underline"
            >
              {{ author.email }}
            </a>
          </div>

          <div v-if="author.institute" class="flex gap-2">
            <strong class="text-slate-900">Inštitúcia:</strong>
            <span>{{ author.institute }}</span>
          </div>
          <div v-else class="text-slate-500 italic">
            Bez inštitúcie
          </div>

          <div v-if="author.url" class="flex gap-2">
            <strong class="text-slate-900">Web:</strong>
            <a
              :href="author.url"
              target="_blank"
              rel="noreferrer"
              class="text-blue-600 hover:underline break-all"
            >
              {{ author.url }}
            </a>
          </div>
          <div v-else class="text-slate-500 italic">
            Bez web stránky
          </div>
        </div>
      </template>
    </Card>

    <Card class="mt-6">
      <template #title>
        Publikácie
      </template>

      <template #content>
        <div v-if="!publicationsByYear?.length" class="text-slate-600">
          Nenašli sa žiadne publikácie od autora.
        </div>

        <div v-else class="space-y-6">
          <Card
            v-for="group in publicationsByYear"
            :key="group.year"
            class="shadow-sm"
          >
            <template #title>
              <div class="text-lg font-semibold text-slate-900">
                {{ group.year }}
              </div>
            </template>

            <template #content>
              <div class="space-y-3">
                <Publication
                  v-for="publication in group.publications"
                  :key="publication.id"
                  :publication="publication"
                />
              </div>
            </template>
          </Card>
        </div>
      </template>
    </Card>
  </div>
</template>

<script setup>
import { Divider } from 'primevue'
import HomeLayout from '@/Layouts/HomeLayout.vue'
import Card from 'primevue/card'
import Publication from '@/Components/Publication.vue'

const props = defineProps({
  author: { type: Object, required: true },
  publicationsByYear: { type: Array, default: () => [] },
})

defineOptions({
  layout: HomeLayout,
})
</script>
