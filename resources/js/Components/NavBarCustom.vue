<script setup>
import { ref } from "vue";
import { Link } from "@inertiajs/vue3";

const mobileOpen = ref(false);

const nav = [
  { label: "Domov", href: "/" },
  { label: "Archív", href: "/archive" },
  { label: "Publikácie", href: "/publications" },
  { label: "Autori", href: "/authors" },
  { label: "O časopise", href: "/about" },
];
</script>

<template>
  <header class="sticky top-0 z-50 w-full border-b bg-white">
    <nav class="mx-auto flex h-24 max-w-7xl items-center justify-between px-6 lg:px-8">
      <Link href="/" class="flex items-center gap-4">
        <img src="/images/omfi-logo.svg" alt="OMFI" class="h-20 w-auto" />
      </Link>
      <div class="hidden items-center gap-10 lg:flex [font-family:'Poppins',sans-serif]">
        <Link
          v-for="item in nav"
          :key="item.href"
          :href="item.href"
          class="text-[16px] font-semibold text-[#161616] capitalize transition-colors hover:text-[#1E4E8C]"
        >
          {{ item.label }}
        </Link>
      </div>
      <button
        @click="mobileOpen = !mobileOpen"
        class="lg:hidden p-2"
        aria-label="Menu"
      >
        <svg v-if="!mobileOpen" xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
        <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </nav>
    <div v-show="mobileOpen" class="border-t bg-white lg:hidden [font-family:'Poppins',sans-serif]">
      <div class="flex flex-col px-6 py-4 gap-3">
        <Link
          v-for="item in nav"
          :key="item.href"
          :href="item.href"
          class="text-[15px] font-semibold text-[#161616] capitalize hover:text-[#D29A2B]"
          @click="mobileOpen = false"
        >
          {{ item.label }}
        </Link>
      </div>
    </div>
  </header>
</template>
