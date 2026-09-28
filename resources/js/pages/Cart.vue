<template>
    <Layout>
        <Head title="Carrito" />
        <div v-if="!lines.length" class="container page">
            <h1 class="page__title">Tu carrito está vacío</h1>
            <p>
                <Link href="/">Ver productos</Link>
            </p>
        </div>

        <div v-else class="container page">
            <h1 class="page__title">Carrito</h1>

            <div class="cart-grid">
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Producto</th>
                            <th>Precio</th>
                            <th>Cantidad</th>
                            <th>Subtotal</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <CartLine v-for="line in lines" :key="line.product.id" :line="line" />
                    </tbody>
                </table>

                <aside class="cart-summary">
                    <h2 class="cart-summary__title">Resumen del pedido</h2>
                    <p>Subtotal: <strong>{{ totals.subtotal.toFixed(2) }}</strong></p>
                    <p>IVA ({{ totals.iva_rate }}%): <strong>{{ totals.iva.toFixed(2) }}</strong></p>
                    <p class="cart-summary__total">Total: <strong>${{ totals.total.toFixed(2) }}</strong></p>
                    <Link href="/pedido" class="btn btn--primary">Tramitar con envío</Link>
                    <Link href="/checkout" class="btn btn--outline">Pedido simple (sin envío)</Link>
                    <p class="cart-summary__note">El envío se calcula al elegir la ciudad de destino.</p>
                </aside>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import Layout from '../components/Layout.vue';
import CartLine from './Cart/CartLine.vue';

defineProps({
    lines: { type: Array, default: () => [] },
    totals: { type: Object, default: () => ({}) },
});
</script>
