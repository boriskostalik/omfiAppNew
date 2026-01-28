<template>
    <div>
        <div class="max-w-6xl mx-auto p-6">
            <Header title="Users"/>
        </div>
      <DataTable
        :value="usersWithoutLogged"
        filterDisplay="menu"
        selectionMode="single"
        dataKey="id"
        tableStyle="min-width: 60rem;"
        class="max-w-6xl mx-auto px-6"
      >
        <Column field="name" header="Name" style="width: 25%">
          <template #body="{ data }" :class="{'bg-red': $page.props.auth.user.role === data.name}">
            {{ data.name }}
          </template>
        </Column>
  
        <Column field="email" header="Email" style="width: 25%">
          <template #body="{ data }">
            {{ data.email }}
          </template>
        </Column>

        <Column class="w-24 !text-end">
          <template #header>
            <div class="ml-auto">
                <Button @click="isModalVisible = true">Pridať</Button>
            </div>
          </template>
          <template #body="{ data }">
            <div class="flex gap-2 !justify-end">
            <Button icon="pi pi-pencil" @click="onRowEdit(data.id)" severity="secondary" rounded></Button>
            <Button icon="pi pi-trash" @click="onRowDelete(data.id)" severity="secondary" rounded></Button>
          </div>
        </template>
    </Column>
  
        <template #empty>
          No Users found.
        </template>
  
      </DataTable>
    </div>
    <UserForm 
      :user="userToEdit" 
      :visible="isModalVisible"
      @close="closeModal()"
    />


  </template>
  
<script setup>
import { computed, ref } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import { DataTable, Column, Paginator, Button, Select } from 'primevue';
import Header from '@/Components/Header.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import UserForm from './UserForm.vue';

defineOptions({
    layout: AuthenticatedLayout,
});
const page = usePage()

const props = defineProps({
    users: {
        type: Object
    }
})

const usersWithoutLogged = computed(()=> props.users.filter(user => user.email !== page.props.auth.user.email))



const isModalVisible = ref(false);
const userToEdit = ref(null);

const closeModal = () => {
  isModalVisible.value = false;
  userToEdit.value = null;
};

const onRowEdit = (id) => {
  isModalVisible.value = true;
  userToEdit.value = props.users.find((p) => p.id === id);
};

const onRowDelete = (id) => {
  if (confirm('Are you sure?')) {
      router.delete(`/dashboard/users/${id}`);
  }
};

</script>

<style scoped>
input {
display: block;
}
</style>
  