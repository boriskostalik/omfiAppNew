<template>
    <Dialog v-model:visible="isVisible" modal :header="user ? 'Úprava autora' : 'Pridanie autora'" :style="{ width: '90vw' }">
        <template #closebutton>
            <Button icon="pi pi-times" class="p-button-text" @click="$emit('close')" />
        </template>
        <form @submit.prevent="submit">
            <div class="grid md:grid-cols-2 sm:grid-cols-1 gap-4">
                <InputText v-model="form.email" placeholder="Email" class="p-2" required />
                <InputText v-model="form.name" placeholder="Name" class="p-2" />
                
                <Select v-model="form.role" :options="['admin', 'editor']" placeholder="Role" class="w-full md:w-56" />
                <br>
                
                <template v-if="!user">
                    <InputText v-model="form.password" placeholder="Password" class="p-2" />
                    <InputText v-model="form.passwordCheck" placeholder="Retype password" class="p-2" required />
                </template>
                
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
import { InputText, Button, Dialog, Checkbox, Select } from 'primevue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    user: {
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
    name: '',
    email: '',
    role: '',
    password: '',
    passwordCheck: '',
});


const submit = () => {
    if (props.user) {
        router.put(`/dashboard/users/${props.user.id}`, form, {
            onError: (err) => {
                console.log(err);
            },
            onSuccess: () => emit('close')
        });
    } else {
        router.post('/dashboard/users', form, {
            onError: (err) => {
                console.log(err);
            },
            onSuccess: () => emit('close')
        });
    }
};

watch(() => props.user, (user) => {
    if (user) {
        form.email = props.user.email;
        form.name = props.user.name,
        form.role = props.user.role;
    } else {
       form.email = '';
       form.name = '';
       form.role = '';
       form.password = '';
       form.passwordCheck = '';
    }
});
</script>