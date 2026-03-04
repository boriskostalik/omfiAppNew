<template>
    <div class="flex flex-col min-h-screen">
        <NavBarCustom />
        <div class="flex-1">
            <slot />
        </div>
        <Footer />
    </div>
</template>
<script setup>
    import NavBarCustom from '@/Components/NavBarCustom.vue';
    import Footer from '@/Components/Footer.vue';
</script>

