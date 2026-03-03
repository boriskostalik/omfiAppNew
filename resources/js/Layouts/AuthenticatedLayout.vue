<script setup>
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import Button from 'primevue/button'
import Drawer from 'primevue/drawer'

const navItems = [
    { label: 'Publikácie', route: 'publications.dashboard', icon: 'pi pi-book' },
    { label: 'Autori', route: 'authors.dashboard', icon: 'pi pi-users' },
    { label: 'Issues', route: 'issues.dashboard', icon: 'pi pi-folder' },
]

const mobileOpen = ref(false)
</script>

<template>
    <div class="flex min-h-screen bg-gray-100">
        <div class="hidden md:flex flex-col bg-[#1E4E8C] text-white w-72 shrink-0 sticky top-0 h-screen">
            <div class="px-6 py-6 border-b border-white/10">
                <span class="font-bold text-2xl tracking-wide">OMFI</span>
                <p class="text-sm text-white/40 mt-1">Administrácia</p>
            </div>

            <nav class="flex-1 py-4 flex flex-col gap-1">
                <Link
                    v-for="item in navItems"
                    :key="item.route"
                    :href="route(item.route)"
                    class="flex items-center gap-4 px-6 py-3.5 text-base font-medium text-white/70 hover:text-white hover:bg-white/10 transition"
                    :class="{ '!text-white !bg-white/20 border-l-4 border-white': route().current(item.route) }"
                >
                    <i :class="[item.icon, 'text-lg']"></i>
                    {{ item.label }}
                </Link>

                <Link
                    v-if="$page.props.auth.user.role === 'admin'"
                    :href="route('users.dashboard')"
                    class="flex items-center gap-4 px-6 py-3.5 text-base font-medium text-white/70 hover:text-white hover:bg-white/10 transition"
                    :class="{ '!text-white !bg-white/20 border-l-4 border-white': route().current('users.dashboard') }"
                >
                    <i class="pi pi-shield text-lg"></i>
                    Users
                </Link>
            </nav>

            <div class="border-t border-white/10 px-6 py-5">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-9 h-9 rounded-full bg-white/20 flex items-center justify-center shrink-0">
                        <i class="pi pi-user text-white text-sm"></i>
                    </div>
                    <div class="overflow-hidden">
                        <p class="text-sm font-semibold text-white truncate">{{ $page.props.auth.user.name }}</p>
                        <p class="text-xs text-white/40 truncate">{{ $page.props.auth.user.email }}</p>
                    </div>
                </div>
                <div class="flex flex-col gap-1">
                    <Link
                        :href="route('profile.edit')"
                        class="flex items-center gap-3 text-sm text-white/60 hover:text-white transition py-1.5"
                    >
                        <i class="pi pi-user-edit text-sm"></i>
                        Profil
                    </Link>
                    <Link
                        :href="route('logout')"
                        method="post"
                        as="button"
                        class="flex items-center gap-3 text-sm text-white/60 hover:text-white transition py-1.5"
                    >
                        <i class="pi pi-sign-out text-sm"></i>
                        Odhlásiť
                    </Link>
                </div>
            </div>
        </div>

        <div class="flex-1 flex flex-col min-w-0">
            <header class="bg-white border-b border-gray-200 h-14 flex items-center px-4 shrink-0">
                <div class="flex items-center gap-3">
                    <Button
                        icon="pi pi-bars"
                        text
                        class="!flex md:!hidden !text-gray-600"
                        @click="mobileOpen = true"
                    />
                    <Link
                        :href="route('home')"
                        class="text-sm text-[#1E4E8C] hover:underline flex items-center gap-1"
                    >
                        <i class="pi pi-arrow-left text-xs"></i>
                        Späť na web
                    </Link>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-6">
                <slot />
            </main>
        </div>

        <Drawer
            v-model:visible="mobileOpen"
            position="left"
            :style="{ width: '280px', background: '#1E4E8C' }"
        >
            <template #header>
                <span class="font-bold text-xl text-white tracking-wide">OMFI</span>
            </template>

            <nav class="flex flex-col gap-1 mt-2">
                <Link
                    v-for="item in navItems"
                    :key="item.route"
                    :href="route(item.route)"
                    @click="mobileOpen = false"
                    class="flex items-center gap-4 px-4 py-3.5 text-base font-medium text-white/70 hover:text-white hover:bg-white/10 transition rounded"
                    :class="{ '!text-white !bg-white/20': route().current(item.route) }"
                >
                    <i :class="[item.icon, 'text-lg']"></i>
                    {{ item.label }}
                </Link>

                <Link
                    v-if="$page.props.auth.user.role === 'admin'"
                    :href="route('users.dashboard')"
                    @click="mobileOpen = false"
                    class="flex items-center gap-4 px-4 py-3.5 text-base font-medium text-white/70 hover:text-white hover:bg-white/10 transition rounded"
                    :class="{ '!text-white !bg-white/20': route().current('users.dashboard') }"
                >
                    <i class="pi pi-shield text-lg"></i>
                    Users
                </Link>
            </nav>
            <div class="absolute bottom-0 left-0 right-0 border-t border-white/10 px-5 py-5">
                <p class="text-sm font-semibold text-white truncate">{{ $page.props.auth.user.name }}</p>
                <p class="text-xs text-white/40 truncate mb-3">{{ $page.props.auth.user.email }}</p>
                <Link
                    :href="route('profile.edit')"
                    class="flex items-center gap-3 text-sm text-white/60 hover:text-white transition py-1.5"
                >
                    <i class="pi pi-user-edit text-sm"></i>
                    Profil
                </Link>
                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="flex items-center gap-3 text-sm text-white/60 hover:text-white transition py-1.5"
                >
                    <i class="pi pi-sign-out text-sm"></i>
                    Odhlásiť
                </Link>
            </div>
        </Drawer>
    </div>
</template>