<template>
    <NavBarCustom />
    <div class="max-w-6xl mx-auto p-6">
        <slot>
        </slot>
    </div>    
   
</template>

<script setup>
    import NavBarCustom from '@/Components/NavBarCustom.vue';

</script>

<style scoped>

</style>