<script setup>
import { computed, ref } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import { Button, DataTable, Column, Dialog } from "primevue";
import Header from "@/Components/Header.vue";
import UserForm from "@/Pages/Dashboard/UserForm.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps({
    users: Object,
});

const page = usePage();

const usersWithoutLogged = computed(() =>
    props.users.filter((user) => user.email !== page.props.auth.user.email),
);

const isModalVisible = ref(false);
const userToEdit = ref(null);
const deleteDialogVisible = ref(false);
const userToDelete = ref(null);

const closeModal = () => {
    isModalVisible.value = false;
    userToEdit.value = null;
};

const onRowEdit = (user) => {
    userToEdit.value = user;
    isModalVisible.value = true;
};

const onRowDelete = (id) => {
    userToDelete.value = id;
    deleteDialogVisible.value = true;
};

const confirmDelete = () => {
    router.delete(route("users.destroy", userToDelete.value));
    deleteDialogVisible.value = false;
    userToDelete.value = null;
};
</script>

<template>
    <div class="max-w-6xl mx-auto px-6 py-4">
        <Header title="Používatelia" />
        <DataTable :value="usersWithoutLogged" dataKey="id">
            <Column field="name" header="Meno" style="width: 30%" />
            <Column field="email" header="Email" style="width: 35%" />
            <Column field="role" header="Rola" style="width: 15%" />
            <Column class="!text-end">
                <template #header>
                    <div class="flex w-full justify-end">
                        <Button
                            icon="pi pi-plus"
                            label="Pridať"
                            severity="success"
                            @click="isModalVisible = true"
                        />
                    </div>
                </template>
                <template #body="{ data }">
                    <div class="flex gap-2 justify-end">
                        <Button
                            icon="pi pi-pencil"
                            @click="onRowEdit(data)"
                            severity="secondary"
                            rounded
                        />
                        <Button
                            icon="pi pi-trash"
                            @click="onRowDelete(data.id)"
                            severity="danger"
                            rounded
                        />
                    </div>
                </template>
            </Column>
            <template #empty>Žiadni používatelia.</template>
        </DataTable>
    </div>
    <Dialog
        v-model:visible="deleteDialogVisible"
        header="Vymazať používateľa"
        :style="{ width: '25rem' }"
        modal
    >
        <p class="mb-4">Naozaj chcete vymazať tohto používateľa?</p>
        <div class="flex justify-end gap-2">
            <Button
                label="Zrušiť"
                severity="secondary"
                @click="deleteDialogVisible = false"
            />
            <Button label="Vymazať" severity="danger" @click="confirmDelete" />
        </div>
    </Dialog>
    <UserForm
        :user="userToEdit"
        :visible="isModalVisible"
        @close="closeModal()"
    />
</template>
