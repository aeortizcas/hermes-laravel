<template>
    <Layout>
        <Head :title="product.name" />
        <div class="container page">
            <CategoryNav :categories="categories" :active-id="product.category?.id ?? null" />

            <div class="product-detail">
                <div class="product-detail__gallery">
                    <img
                        v-if="images[activeImage]"
                        class="product-detail__main-image"
                        :src="images[activeImage]"
                        :alt="product.name"
                    />
                    <div v-else class="product-detail__no-image">Sin imagen</div>
                    <div v-if="images.length > 1" class="product-detail__thumbs">
                        <button
                            v-for="(src, i) in images"
                            :key="src + i"
                            type="button"
                            :class="i === activeImage ? 'is-active' : ''"
                            @click="activeImage = i"
                            :aria-label="`Imagen ${i + 1}`"
                        >
                            <img :src="src" alt="" />
                        </button>
                    </div>
                </div>

                <div class="product-detail__info">
                    <Link
                        v-if="product.category"
                        class="product-detail__category"
                        :href="`/categorias/${product.category.id}`"
                    >
                        {{ product.category.name }}
                    </Link>
                    <h1 class="product-detail__title">{{ product.name }}</h1>
                    <p v-if="product.description" class="product-detail__description">
                        {{ product.description }}
                    </p>

                    <template v-if="product.available">
                        <p class="product-detail__price">
                            <Price :value="product.price" />
                        </p>
                        <p class="product-detail__stock">
                            {{ product.stock <= 10 ? `Solo quedan ${product.stock} unidades` : `En stock · ${product.stock} unidades disponibles` }}
                        </p>

                        <form class="product-detail__buy" @submit.prevent="addToCart">
                            <label for="qty">Cantidad</label>
                            <input
                                id="qty"
                                type="number"
                                min="1"
                                :max="maxQty"
                                :value="form.qty"
                                @change="e => form.qty = e.target.value"
                            />
                            <p v-if="form.errors.qty" class="form-error">{{ form.errors.qty }}</p>
                            <button type="submit" :disabled="form.processing">
                                {{ form.processing ? 'Agregando…' : 'Agregar al carrito' }}
                            </button>
                        </form>
                    </template>
                    <p v-else class="product-detail__soldout">Producto agotado (AGOTADO)</p>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import Layout from '../components/Layout.vue';
import CategoryNav from '../components/CategoryNav.vue';
import Price from '../components/Price.vue';

const props = defineProps({
    product: { type: Object, required: true },
    categories: { type: Array, default: () => [] },
});

const activeImage = ref(0);
const form = useForm({ qty: 1 });

const images = computed(() =>
    props.product.images?.length
        ? props.product.images
        : props.product.cover
          ? [props.product.cover]
          : []
);
const maxQty = computed(() => Math.min(99, props.product.stock));

function addToCart() {
    form.transform((values) => ({ ...values, product_id: props.product.id }));
    form.post('/carrito/agregar', { preserveScroll: true });
}
</script>
