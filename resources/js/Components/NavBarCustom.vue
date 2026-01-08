<template>
  <header class="sticky top-0 z-[80]  bg-white border-b">

    <div class="max-w-6xl mx-auto px-4">
      <div class="h-16 lg:h-24 flex items-center justify-between">

        <!-- Logo -->
        <Link href="/" class="flex items-center">
          <img src="/images/omfi-logo.svg" class="h-8 lg:h-12" />
        </Link>

        <!-- Desktop menu -->
        <nav class="hidden md:flex h-full">
          <Link
            v-for="i in items"
            :key="i.href"
            :href="i.href"
            :class="linkClass(i.href)"
            class="flex items-center justify-center h-full px-8 text-lg lg:text-xl font-semibold transition-colors"
          >
            {{ i.label }}
          </Link>
        </nav>

        <!-- Mobile button (hamburger <-> X) -->
        <button
          class="md:hidden inline-flex items-center justify-center h-10 w-10 rounded-xl
                 hover:bg-slate-100 transition"
          @click="open = !open"
          aria-label="Menu"
        >
          <!-- hamburger -->
          <svg v-if="!open" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-slate-800" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
          </svg>

          <!-- X -->
          <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-slate-800" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
    </div>

    <!-- ✅ OVERLAY + ABSOLUTE MENU (neposunie stránku) -->
   <div v-if="open" class="md:hidden">
  <!-- overlay (pod navbarom) -->
 

  <!-- menu panel (nad overlayom, pod navbarom) -->
  <div
    class="absolute left-0 right-0 top-full z-[55] border-b bg-white shadow-lg"
  >
    <div class="max-w-6xl mx-auto px-4 py-3 flex flex-col">
      <Link
        v-for="i in items"
        :key="i.href"
        :href="i.href"
        :class="linkClass(i.href)"
        class="rounded-xl px-4 py-4 text-lg font-semibold transition-colors"
        @click="open = false"
      >
        {{ i.label }}
      </Link>
    </div>
  </div>
</div>

  </header>
</template>

<script setup>
import { ref } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'

const open = ref(false)
const page = usePage()

const items = [
  { label: 'Domov', href: '/' },
  { label: 'Publikácie', href: '/publications' },
  { label: 'Autori', href: '/authors' },
  { label: 'O Časopise', href: '/about' },
]

const linkClass = (href) => {
  const active = page.url === href || page.url.startsWith(href + '/')
  return active
    ? 'bg-[rgba(107,176,215,1)] text-white'
    : 'text-slate-700 hover:bg-[rgba(211,227,242,0.8)]'
}
</script>
