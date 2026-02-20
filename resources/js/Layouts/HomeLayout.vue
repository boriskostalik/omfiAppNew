<template>
    <NavBarCustom />
    <div class="">
        <slot>
        </slot>
    </div>    
    <Footer />
</template>
<script setup>
    import NavBarCustom from '@/Components/NavBarCustom.vue';
    import Footer from '@/Components/Footer.vue';
</script>

