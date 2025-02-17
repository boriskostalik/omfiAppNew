<template>
  <!-- PrimeVue Menubar -->
  <Menubar :model="items" class="bg-gray-800 text-white custom-menubar" />
</template>

<script setup>
import { router } from '@inertiajs/vue3';  // Importujeme router z Inertia.js
import Menubar from 'primevue/menubar';    // Importujeme Menubar komponentu

// Definovanie položiek menu
const items = [
  {
    label: 'Domov',
    icon: 'pi pi-home',
    command: () => router.get('/')  // Navigácia na domovskú stránku
  },
  {
    label: 'Publikácie',
    icon: 'pi pi-book',
    command: () => router.get('/publications')  // Navigácia na stránku publikácií
  },
  {
    label: 'Autori',
    icon: 'pi pi-user',
    command: () => router.get('/authors')  // Navigácia na stránku autorov
  },
  {
    label: 'O Časopise',
    icon: 'pi pi-info-circle',
    command: () => router.get('/about')  // Navigácia na stránku O Časopise
  }
];
</script>

<style scoped>
/* Zvýšenie výšky navbaru na desktopoch */
@media (min-width: 960px) {
  .custom-menubar {
    display: flex;
    justify-content: center;
    height: 90px; /* Zvýšenie výšky navbaru */
    align-items: center;
  }

  /* Zvýšenie výšky samotných položiek v navbar */
  :deep(.p-menubar-root-list) {
    height: 100%;
    display: flex;
    align-items: center;
    gap: 30px; /* Medzera medzi položkami */
  }

  /* Väčší padding pre lepšie vycentrovanie textu */
  :deep(.p-menuitem) {
    padding: 20px 30px; /* Väčšie medzery medzi položkami */
    font-size: 10 rem; /* Väčšie písmo */
    font-weight: 600; /* Tučnejší text */
  }

  /* Zvýšenie veľkosti ikon v menu */
  :deep(.p-menuitem-icon) {
    font-size: 1.4rem;
  }
}
</style>
