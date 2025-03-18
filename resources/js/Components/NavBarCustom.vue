<template>
  <!-- PrimeVue Menubar -->
  <Menubar :model="items" class="bg-gray-800 text-white custom-menubar rounded-t-none" />
</template>

<script setup>
import { router } from '@inertiajs/vue3';  
import Menubar from 'primevue/menubar';   


const items = [
  {
    label: 'Domov',
    icon: 'pi pi-home',
    command: () => router.get('/')  
  },
  {
    label: 'Publikácie',
    icon: 'pi pi-book',
    command: () => router.get('/publications') 
  },
  {
    label: 'Autori',
    icon: 'pi pi-user',
    command: () => router.get('/authors')  
  },
  {
    label: 'O Časopise',
    icon: 'pi pi-info-circle',
    command: () => router.get('/about')  
  }
];
</script>

<style scoped>
/* Zvýšenie výšky navbaru na desktopoch */
@media (min-width: 960px) {
  .custom-menubar {
    display: flex;
    justify-content: center;
    height: 90px; 
    align-items: center;
  }


  :deep(.p-menubar-root-list) {
    height: 100%;
    display: flex;
    align-items: center;
    gap: 30px; 
  }


  :deep(.p-menuitem) {
    padding: 20px 30px;
    font-size: 10 rem;
    font-weight: 600; 
  }

 
  :deep(.p-menuitem-icon) {
    font-size: 1.4rem;
  }
}
</style>
