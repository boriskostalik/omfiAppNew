<template>
    <Dialog v-model:visible="isVisible" modal :header="author ? 'Úprava autora' : 'Pridanie autora'" :style="{ width: '90vw' }">
        <template #closebutton>
            <Button icon="pi pi-times" class="p-button-text" @click="$emit('close')" />
        </template>
        <form @submit.prevent="submit">
            <div class="grid md:grid-cols-2 sm:grid-cols-1 gap-4">
                <InputText v-model="form.surname" placeholder="Surname" class="p-2" required />
                <InputText v-model="form.von" placeholder="Von" class="p-2" />
                
                <InputText v-model="form.firstname" placeholder="First Name" class="p-2" required />
                <InputText v-model="form.email" placeholder="Email" class="p-2" type="email" required />
                
                <InputText v-model="form.url" placeholder="URL" class="p-2" />
                <InputText v-model="form.institute" placeholder="Institute" class="p-2" required />
                
                <div class="flex items-center gap-2">
                    <Checkbox v-model="specialcharsValue" :binary="true" />
                    <label>Special Characters</label>
                </div>
                <InputText v-model="cleanName" placeholder="Clean Name" class="p-2" />
            </div>

            <div class="flex items-center gap-4 mt-4">
                <Button label="Cancel" @click="$emit('close')" text class="!p-4 w-full"></Button>
                <Button type="submit" label="Submit" text class="!p-4 w-full !bg-primary !text-white" />
            </div>
        </form>
    </Dialog>
</template>

<script setup>
import { computed, reactive, watch, ref } from 'vue';
import { InputText, Button, Dialog, Checkbox } from 'primevue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    author: {
        type: Object,
        default: null
    },
    visible: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['close']);
const isVisible = computed(()=> props.visible);

const form = reactive({
    surname: '',
    von: '',
    firstname: '',
    email: '',
    url: '',
    institute: '',
    specialchars: 0
});

const specialcharsValue = computed({
    get() {
        return form.specialchars === 1;
    },
    set(value) {
        form.specialchars = value ? 1 : 0;
    }
});

const cleanName = computed({
    get() {
        let name = '';
        
        if (form.von) {
            name += form.von + ' ';
        }
            name += form.surname;
        
        if (form.firstname) {
            name += ', ' + form.firstname;
        }
        
        return name.trim();
    },
    set(value) {
        const parts = value.split(',').map(part => part.trim());
        
        if (parts.length > 1) {
            const surnameParts = parts[0].split(' ');
            form.firstname = parts[1];
            
            if (surnameParts.length > 1) {
                form.surname = surnameParts[surnameParts.length - 1];
                form.von = surnameParts.slice(0, -1).join(' ');
            } else {
                form.surname = surnameParts[0];
                form.von = '';
            }
        } else {
            const nameParts = parts[0].split(' ');
            form.surname = nameParts[nameParts.length - 1];
            form.firstname = nameParts.slice(0, -1).join(' ');
        }
    }
});

const submit = () => {
    if (props.author) {
        form.cleanname = cleanName.value;
        router.put(`/dashboard/authors/${props.author.id}`, form, {
            onError: (err) => {
                console.log(err);
            },
            onSuccess: () => emit('close')
        });
    } else {
        form.cleanname = cleanName.value;
        router.post('/dashboard/authors', form, {
            onError: (err) => {
                console.log(err);
            },
            onSuccess: () => emit('close')
        });
    }
};

watch(() => props.author, (author) => {
    if (author) {
        form.surname = author.surname || '';
        form.von = author.von || '';
        form.firstname = author.firstname || '';
        form.email = author.email || '';
        form.url = author.url || '';
        form.institute = author.institute || '';
        form.specialchars = author.specialchars || 0;
    } else {
        // Reset form when no author is selected
        form.surname = '';
        form.von = '';
        form.firstname = '';
        form.email = '';
        form.url = '';
        form.institute = '';
        form.specialchars = 0;
    }
});
</script>