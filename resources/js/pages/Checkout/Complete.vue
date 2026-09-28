<template>
    <Layout>
        <Head title="Pedido recibido" />
        <div class="container page">
            <div class="order-complete">
                <h1 class="page__title">¡Pedido recibido!</h1>
                <p>Gracias <strong>{{ order.customer.name }}</strong>. Tu pedido <strong>#{{ order.id }}</strong> quedó guardado con estado pendiente.</p>

                <ul class="order-complete__items">
                    <li v-for="item in order.items" :key="item.name">{{ item.qty }} × {{ item.name }} — {{ money(item.subtotal) }}</li>
                </ul>

                <p class="order-complete__total">Total (IVA {{ order.totals.iva_rate }}% incluido): <strong>{{ money(order.totals.total) }}</strong></p>

                <Link href="/" class="btn btn--primary">
                    Seguir comprando
                </Link>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import Layout from '../../components/Layout.vue';
import { money } from '../../components/format';

defineProps({
    order: { type: Object, required: true },
});
</script>
