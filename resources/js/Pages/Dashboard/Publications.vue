<template>
<div>
    <h1 class="text-2xl font-bold mb-4">Publications Management</h1>

    <form @submit.prevent="submit">
    <input v-model="form.title" placeholder="Title" class="border p-2 mb-2" required />
    <input v-model="form.year" placeholder="Year" class="border p-2 mb-2" required />
    <input v-model="form.bibtex_id" placeholder="BibTeX ID" class="border p-2 mb-2" required />
    <button type="submit" class="bg-blue-500 text-white p-2">Add Publication</button>
    </form>

    <ul class="mt-6">
    <li v-for="publication in publications.data" :key="publication.id" class="mb-2">
        {{ publication.title }} ({{ publication.year }})
        <button @click="edit(publication)" class="text-blue-500 ml-2">Edit</button>
        <button @click="destroy(publication.id)" class="text-red-500 ml-2">Delete</button>
    </li>
    </ul>
</div>
</template>

<script setup>
    import { reactive } from 'vue';
    import { router } from '@inertiajs/vue3';
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    defineOptions({
    layout: AuthenticatedLayout,
    });

    const props = defineProps({ publications: Object });

    const form = reactive({
    title: '',
    year: '',
    bibtex_id: ''
    });

    const submit = () => {
    router.post('/dashboard/publications', form);
    };

    const edit = (publication) => {
    Object.assign(form, publication);
    };

    const destroy = (id) => {
        if (confirm('Are you sure?')) {
            router.delete(`/dashboard/publications/${id}`);
        }
    };
</script>

<style scoped>
input {
display: block;
}
</style>