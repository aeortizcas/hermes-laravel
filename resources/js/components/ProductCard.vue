<template>
    <article class="product-card">
        <Link :href="`/producto/${product.id}`" class="product-card__media">
            <img v-if="product.cover" :src="product.cover" :alt="product.name" loading="lazy" />
            <div v-else class="product-card__placeholder">Sin imagen</div>
        </Link>
        <div class="product-card__body">
            <h3 class="product-card__title">
                <Link :href="`/producto/${product.id}`">{{ product.name }}</Link>
            </h3>
            <template v-if="product.available">
                <p class="product-card__price">
                    <Price :value="product.price" />
                </p>
                <p class="product-card__stock" :class="product.stock <= 10 ? 'product-card__stock--low' : ''">
                    {{ product.stock <= 10 ? `Solo quedan ${product.stock} unidades` : 'En stock' }}
                </p>
                <div class="product-card__add">
                    <button type="button" class="btn btn--primary btn--small" @click="addToCart">
                        Agregar al carrito
                    </button>
                </div>
            </template>
            <p v-else class="product-card__soldout">Agotado</p>
        </div>
    </article>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3';
import Price from './Price.vue';

const props = defineProps({
    product: { type: Object, required: true },
});

function addToCart() {
    router.post(
        '/carrito/agregar',
        { product_id: props.product.id, qty: 1 },
        { preserveScroll: true }
    );
}
</script>
