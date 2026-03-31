<script setup>
import { router } from "@inertiajs/vue3";
import Card from "primevue/card";
const props = defineProps({
    author: { type: Object, required: true },
});
const goToAuthor = (id) => {
    router.get(route("authors.detail", id));
};
</script>
<template>
    <Card
        class="mb-4 cursor-pointer hover:shadow-xl transition-shadow"
        @click="goToAuthor(author.id)"
    >
        <template #title>
            <div class="flex items-center gap-3">
                <span>{{ author.firstname }} {{ author.surname }}</span>
            </div>
        </template>
        <template #content>
            <div class="space-y-2 text-sm text-gray-600">
                <div v-if="author.email">
                    <strong>Email:</strong>
                    <a
                        :href="'mailto:' + author.email"
                        class="text-blue-600 hover:underline ml-1"
                        @click.stop
                    >
                        {{ author.email }}
                    </a>
                </div>
                <div v-if="author.institute">
                    <strong>Institute:</strong>
                    <span class="ml-1">{{ author.institute }}</span>
                </div>
                <div v-else class="text-gray-500 italic">
                    No institute specified
                </div>
                <div v-if="author.url">
                    <strong>Website:</strong>
                    <a
                        :href="author.url"
                        target="_blank"
                        rel="noreferrer"
                        class="text-blue-600 hover:underline ml-1 break-all"
                        @click.stop
                    >
                        {{ author.url }}
                    </a>
                </div>
                <div v-else class="text-gray-500 italic">
                    No website available
                </div>
            </div>
        </template>
    </Card>
</template>
