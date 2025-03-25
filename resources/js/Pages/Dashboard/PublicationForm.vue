 <template>
<div>
    <form @submit.prevent="submit">
        <div class="grid md:grid-cols-2 sm:grid-cols-1 gap-4">
            <Dropdown v-model="form.type" :options="types" placeholder="Type of publication" class="w-full" />

            <InputText v-model="form.title" placeholder="Title" class="p-2" required />
            <InputText v-model="form.title_eng" placeholder="English Title" class="p-2" />

            <InputText v-model="form.mesc" placeholder="MESC" class="p-2" />
            <InputText v-model="form.bibtex_id" placeholder="BibTeX ID" class="p-2" />

            <InputText v-model="form.year" placeholder="Year" class="p-2" required />
            <InputText v-model="form.actualyear" placeholder="Actual Year" class="p-2" />

            <InputText v-model="form.journal" placeholder="Journal" class="p-2" />
            <InputText v-model="form.volume" placeholder="Volume" class="p-2" />

            <InputText v-model="form.number" placeholder="Number" class="p-2" />
            <Dropdown v-model="form.month" :options="months" placeholder="Month" class="w-full" />

            <InputText v-model="form.firstpage" placeholder="First Page" class="p-2" />
            <InputText v-model="form.lastpage" placeholder="Last Page" class="p-2" />

            <InputText v-model="form.issn" placeholder="ISSN" class="p-2" />
            <InputText v-model="form.isbn" placeholder="ISBN" class="p-2" />

            <InputText v-model="form.url" placeholder="URL" class="p-2" />
            <InputText v-model="form.doi" placeholder="DOI" class="p-2" />

            <InputText v-model="form.crossref" placeholder="Crossref" class="p-2" />
            <InputText v-model="form.namekey" placeholder="Key" class="p-2" />

            <InputText v-model="form.keywords" placeholder="Keywords" class="p-2" />
            <Textarea v-model="form.abstract" placeholder="Abstract" rows="5" class="p-2" />
        </div>

    <Button type="submit" label="Submit" class="mt-4" />
    </form>
</div>
 </template>

 <script setup>
    import { reactive } from 'vue';
    // import { router } from '@inertiajs/inertia-vue3';
    const props = defineProps({
        publication: {
            type: Object,
        },
    });
    const form = reactive({
    type: '',
    title: '',
    title_eng: '',
    mesc: '',
    bibtex_id: '',
    year: '',
    actualyear: '',
    journal: 'Obzory matematiky, fyziky a informatiky',
    volume: '',
    number: '',
    month: '',
    firstpage: '',
    lastpage: '',
    issn: '',
    isbn: '',
    url: '',
    doi: '',
    crossref: '',
    namekey: '',
    keywords: '',
    abstract: '',
    entered_by: props.user.id.toString(),
    });

    const types = [
        'Article', 'Book', 'Booklet', 'Inbook', 'Incollection', 'Inproceedings', 'Manual',
        'Mastersthesis', 'Misc', 'Phdthesis', 'Proceedings', 'Techreport', 'Unpublished'
    ];

    const months = [
        'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August',
        'September', 'October', 'November', 'December', 'not known'
    ];

    const submit = () => {
        router.post('/dashboard/publications', form, {
            onError: (err) => {
                console.log(err);
            },
        });
    };

    const destroy = (id) => {
        if (confirm('Are you sure?')) {
            router.delete(`/dashboard/publications/${id}`);
        }
    };
</script>