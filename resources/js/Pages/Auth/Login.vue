<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';

defineOptions({
  layout: (_h, page) => page,
})

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const showContactDialog = ref(false);

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Prihlásenie" />

        <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="email" value="Email" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Heslo" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4 block">
                <label class="flex items-center">
                    <Checkbox name="remember" v-model:checked="form.remember" />
                    <span class="ms-2 text-sm text-gray-600">Zapamätať si ma</span>
                </label>
            </div>

            <div class="mt-4 flex items-center justify-end">
                <button
                    type="button"
                    @click="showContactDialog = true"
                    class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none"
                >
                    Zabudli ste heslo?
                </button>

                <PrimaryButton
                    class="ms-4"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Prihlásiť sa
                </PrimaryButton>
            </div>
        </form>

        <div
            v-if="showContactDialog"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
            @click.self="showContactDialog = false"
        >
            <div class="bg-white rounded-xl shadow-xl px-8 py-6 max-w-sm w-full mx-4">
                <h2 class="text-lg font-semibold text-gray-900 mb-2">Obnovenie hesla</h2>
                <p class="text-sm text-gray-600">
                    Pre obnovenie hesla kontaktujte správcu na adrese
                    <a href="mailto:mdrlik@ukf.sk" class="text-[#1E4E8C] font-medium underline">mdrlik@ukf.sk</a>.
                </p>
                <div class="mt-5 flex justify-end">
                    <button
                        @click="showContactDialog = false"
                        class="px-4 py-2 text-sm font-medium text-white bg-[#1E4E8C] rounded-lg hover:bg-[#184073] transition-colors"
                    >
                        Zavrieť
                    </button>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>
