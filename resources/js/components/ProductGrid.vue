<template>
    <p v-if="!products?.length" class="store-empty">No hay productos que mostrar.</p>
    <div v-else class="product-grid">
        <ProductCard v-for="p in products" :key="p.id" :product="p" />
    </div>
</template>

<script setup>
import ProductCard from './ProductCard.vue';

defineProps({
    products: { type: Array, default: () => [] },
});
</script>
