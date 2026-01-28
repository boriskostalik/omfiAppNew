<script setup>
import { router } from '@inertiajs/vue3'
import Card from 'primevue/card'
import Chip from 'primevue/chip'
import Tag from 'primevue/tag'

const props = defineProps({
  publication: { type: Object, required: true },
  withAuthors: { type: Boolean, default: true },
})

const goToAuthor = (id) => {
  router.get(route('authors.detail', id))
}

const goToPublication = (id) => {
  router.get(route('publications.detail', id))
}
</script>

<template>
  <Card
    class="mb-4 cursor-pointer hover:shadow-xl transition-shadow"
    @click="goToPublication(publication.id)"
  >
    <template #title>
      {{ publication.title }}
    </template>

    <template #content>
      <div v-if="withAuthors" class="flex flex-wrap items-center gap-3 mb-4">
      

        <template v-if="publication.authors?.length">
          <Chip
            v-for="author in publication.authors"
            :key="author.id"
            class="cursor-pointer text-base px-3 py-2"
            @click.stop="goToAuthor(author.id)"
          >
            {{ author.cleanname }}

            <Tag
              v-if="author.is_editor === 'Y'"
              value="Editor"
              severity="secondary"
              class="ml-2"
            />
          </Chip>
        </template>

        <span v-else class="text-gray-500 text-sm">
          Neznámi autori
        </span>
      </div>

      <div class="flex flex-wrap gap-4 text-sm text-gray-600">
        <span><strong>Rok:</strong> {{ publication.year }}</span>

        <span>
          <strong>Vydanie:</strong> {{ publication.number }}
        </span>

        <span v-if="publication.firstpage && publication.lastpage">
          <strong>Strany:</strong> {{ publication.firstpage }}–{{ publication.lastpage }}
        </span>
      </div>
    </template>
  </Card>
</template>
