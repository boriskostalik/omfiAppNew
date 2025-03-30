<template>
    <Dialog v-model:visible="isVisible" modal :header="publication ? 'Úprava publikácie' : 'Pridanie publikácie'" :style="{ width: '90vw' }">
        <template #closebutton>
            <Button icon="pi pi-times" class="p-button-text" @click="$emit('close')" />
        </template>
        <form @submit.prevent="submit">
            <div class="grid md:grid-cols-2 sm:grid-cols-1 gap-4">
                <Dropdown v-model="form.type" :options="types" placeholder="Type of publication" class="w-full" />

                <InputText v-model="form.title" placeholder="Title" class="p-2" required />
                <InputText v-model="form.title_eng" placeholder="English Title" class="p-2" />

                <InputText v-model="form.mesc" placeholder="MESC" class="p-2" />
                <InputText v-model="form.bibtex_id" placeholder="BibTeX ID" class="p-2" />

                <InputText v-model="form.year" placeholder="Year" class="p-2" required />
                <InputText v-model="form.actualyear" placeholder="Actual Year" class="p-2" required />

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

                <br>
                <MultiSelect v-model="form.authors" :options="mappedAuthors" optionLabel="cleanname" filter placeholder="Authors" display="chip"
                    :maxSelectedLabels="5" class="w-full h-full" />
                <MultiSelect v-model="form.editors" :options="form.authors" optionLabel="cleanname" filter placeholder="Editors" display="chip"
                    :maxSelectedLabels="5" class="w-full h-full" />
            </div>

            <div class="flex items-center gap-4 mt-4">
                <Button label="Cancel" @click="$emit('close')" text class="!p-4 w-full"></Button>
                <Button type="submit" label="Submit" text class="!p-4 w-full !bg-primary !text-white" />
        </div>
        </form>
    </Dialog>
</template>

 <script setup>
    import { computed, reactive, watch, defineEmits } from 'vue';
    import { InputText, Textarea, Dropdown, Button, Dialog, MultiSelect } from 'primevue';
    import { router } from '@inertiajs/vue3';
    // import { router } from '@inertiajs/inertia-vue3';
    const props = defineProps({
        publication: {
            type: Object,
        },
        authors: {
            type: Array,
            default: () => [],
        },
        visible: {
            type: Boolean,
            default: false,
        },
        entered_by: {
            type: Number,
            default: 0,
        },
    });
    const emit = defineEmits(['close']);

    const isVisible = computed(()=> props.visible);
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
        entered_by: props.entered_by,
        authors: [],
        editors: [],
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
        const submitData = {
        ...form,
        authors: form.authors.map(author => ({
            id: author.id,
            is_editor: form.editors.some(editor => editor.id === author.id) ? 'Y' : 'N'
            }))
        };
    console.log(submitData);
        if(props.publication) {
            router.put(`/dashboard/publications/${props.publication.id}`, submitData, {
                onError: (err) => {
                    console.log(err);
                },
                onSuccess: () => emit('close'),
            });
        }else{
            router.post('/dashboard/publications', submitData, {
                onError: (err) => {
                    console.log(err);
                },
                onSuccess: () => emit('close'),
            });
        }
    };

    const mappedAuthors = computed(() => {
        return props.authors.map((author) => ({
            id: author.id,
            cleanname: author.cleanname,
        }));
    });

    watch(() => props.publication, (publication) => {
        if (publication) {
            form.type = publication.type;
            form.title = publication.title;
            form.title_eng = publication.title_eng;
            form.mesc = publication.mesc;
            form.bibtex_id = publication.bibtex_id;
            form.year = publication.year;
            form.actualyear = publication.actualyear;
            form.journal = publication.journal;
            form.volume = publication.volume;
            form.number = publication.number;
            form.month = publication.month;
            form.firstpage = publication.firstpage;
            form.lastpage = publication.lastpage;
            form.issn = publication.issn;
            form.isbn = publication.isbn;
            form.url = publication.url;
            form.doi = publication.doi;
            form.crossref = publication.crossref;
            form.namekey = publication.namekey;
            form.keywords = publication.keywords;
            form.abstract = publication.abstract;
            form.authors = publication.authors.map(author => ({
                id: author.id,
                cleanname: author.cleanname,
            }));
           form.editors = publication.authors.filter(author => author.pivot .is_editor === 'Y').map(editor => ({
                id: editor.id,
                cleanname: editor.cleanname,
            }));
        }
        else {
            form.type = '';
            form.title = '';
            form.title_eng = '';
            form.mesc = '';
            form.bibtex_id = '';
            form.year = '';
            form.actualyear = '';
            form.journal = 'Obzory matematiky, fyziky a informatiky';
            form.volume = '';
            form.number = '';
            form.month = '';
            form.firstpage = '';
            form.lastpage = '';
            form.issn = '';
            form.isbn = '';
            form.url = '';
            form.doi = '';
            form.crossref = '';
            form.namekey = '';
            form.keywords = '';
            form.abstract = '';
            form.authors = [];
            form.editors = [];
        }
    });
</script>  