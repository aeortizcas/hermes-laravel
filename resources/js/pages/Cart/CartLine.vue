<template>
    <tr>
        <td>
            <Link :href="`/producto/${product.id}`" class="cart-line__thumb">
                <img v-if="product.cover" :src="product.cover" :alt="product.name" loading="lazy" />
                <span v-else class="product-card__placeholder">Sin imagen</span>
            </Link>
        </td>
        <td>
            <Link :href="`/producto/${product.id}`" class="cart-line__name">{{ product.name }}</Link>
        </td>
        <td class="cart-line__price">
            <Price :value="product.price" />
        </td>
        <td>
            <form class="cart-line__qty" @submit.prevent="updateQty">
                <input
                    type="number"
                    min="0"
                    max="99"
                    :value="form.qty"
                    @change="e => form.qty = e.target.value"
                    :aria-label="`Cantidad de ${product.name}`"
                />
                <button type="submit" :disabled="form.processing">Actualizar</button>
            </form>
        </td>
        <td>
            <Price :value="subtotal" />
        </td>
        <td>
            <button
                type="button"
                class="cart-line__remove"
                @click="removeLine"
                :disabled="remove.processing"
            >
                Quitar
            </button>
        </td>
    </tr>
</template>

<script setup>
import { computed } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import Price from '../../components/Price.vue';

const props = defineProps({
    line: { type: Object, required: true },
});

const product = computed(() => props.line.product);
const { qty, subtotal } = props.line;

const form = useForm({ qty });
const remove = useForm({});

function updateQty() {
    form.post(`/carrito/${product.value.id}`, { preserveScroll: true });
}

function removeLine() {
    router.delete(`/carrito/${product.value.id}`, { preserveScroll: true });
}
</script>
