<template>
    <AdminLayout title="Dashboard">
        <div class="admin__content">
            <h1 class="page__title">Dashboard</h1>
            <div class="admin-cards">
                <div v-for="c in cards" :key="c.label" class="admin-card">
                    <span class="admin-card__value">{{ c.value }}</span>
                    <span class="admin-card__label">{{ c.label }}</span>
                    <Link v-if="c.href" :href="c.href" class="admin-card__link">Ver →</Link>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import AdminLayout from './AdminLayout.vue';

const props = defineProps({
    counts: { type: Object, required: true },
});

const cards = computed(() => [
    { label: 'Productos', value: props.counts.products, href: '/admin/productos' },
    { label: 'Categorías', value: props.counts.categories },
    { label: 'Clientes', value: props.counts.customers },
    { label: 'Pedidos', value: props.counts.orders },
    { label: 'Envíos', value: props.counts.shipments },
    { label: 'Envíos pendientes', value: props.counts.pendingShipments },
]);
</script>
