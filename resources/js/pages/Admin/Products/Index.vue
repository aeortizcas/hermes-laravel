<template>
    <AdminLayout title="Productos">
        <Head title="Productos" />
        <div class="admin__content">
            <div class="admin__head">
                <h1 class="page__title">Productos</h1>
                <Link href="/admin/productos/nuevo" class="btn btn--primary">+ Nuevo producto</Link>
            </div>

            <table class="cart-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Estado</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="p in products" :key="p.id">
                        <td>{{ p.id }}</td>
                        <td>{{ p.name }}</td>
                        <td>{{ p.category ?? '—' }}</td>
                        <td>{{ p.price !== null ? money(p.price) : '—' }}</td>
                        <td>{{ p.stock }}</td>
                        <td>
                            <span v-if="p.estado !== 0" class="badge badge--done">Activo</span>
                            <span v-else class="badge badge--pending">Inactivo</span>
                        </td>
                        <td><Link :href="`/admin/productos/${p.id}/editar`">Editar</Link> · <button type="button" class="admin__danger" @click="remove(p)">Desactivar</button></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AdminLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '../AdminLayout.vue';
import { money } from '../../../components/format';

defineProps({
    products: { type: Array, default: () => [] },
});

function remove(product) {
    if (confirm(`¿Desactivar el producto “${product.name}”?`)) {
        router.delete(`/admin/productos/${product.id}`);
    }
}
</script>
