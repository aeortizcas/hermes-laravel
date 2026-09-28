<template>
    <Layout>
        <Head :title="category.name" />
        <div class="container page">
            <CategoryNav :categories="categories" :active-id="category.id" />
            <h1 class="page__title">{{ category.name }}</h1>
            <ProductGrid :products="products" />
        </div>
    </Layout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import Layout from '../components/Layout.vue';
import CategoryNav from '../components/CategoryNav.vue';
import ProductGrid from '../components/ProductGrid.vue';

defineProps({
    category: { type: Object, required: true },
    products: { type: Array, default: () => [] },
    categories: { type: Array, default: () => [] },
});
</script>
