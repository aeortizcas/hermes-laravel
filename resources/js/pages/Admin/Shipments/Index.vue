<template>
    <AdminLayout title="Envíos">
        <Head title="Envíos" />
        <div class="admin__content">
            <h1 class="page__title">Envíos (tbl_rastreo)</h1>

            <nav class="category-nav admin-tabs">
                <Link
                    v-for="[value, label] in filters"
                    :key="value"
                    :href="`/admin/envios?status=${value}`"
                    :class="status === value ? 'is-active' : ''"
                >
                    {{ label }}
                </Link>
            </nav>

            <table class="cart-table admin-shipments">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Producto</th>
                        <th>Empresa</th>
                        <th>Pago</th>
                        <th>Total</th>
                        <th>Fechas (sol/pro/enc/ent)</th>
                        <th>Etapa</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="s in shipments" :key="s.id">
                        <td>
                            <Link :href="`/admin/envios/${s.id}`">#{{ s.id }}</Link>
                        </td>
                        <td>
                            {{ s.cliente || s.usuario }}
                            <p class="shipment-table__meta">{{ s.usuario }}</p>
                        </td>
                        <td>
                            {{ s.producto }}
                            <p class="shipment-table__meta">{{ s.cantidad }} u · {{ s.ciudad }}</p>
                        </td>
                        <td>{{ s.empresa }}</td>
                        <td>{{ s.metodoPago }}</td>
                        <td>{{ money(s.total) }}</td>
                        <td class="shipment-dates">
                            <span title="Solicitud">{{ s.fechasol ?? '—' }}</span>
                            <span title="Procesado">{{ s.fechapro ?? '—' }}</span>
                            <span title="Encargo">{{ s.fechaencargo ?? '—' }}</span>
                            <span title="Entrega">{{ s.fechaentrega ?? '—' }}</span>
                        </td>
                        <td>
                            <span v-if="s.etapa === 'delivered'" class="badge badge--done">{{ STAGE_LABEL[s.etapa] ?? s.etapa }}</span>
                            <span v-else-if="s.etapa === 'pending'" class="badge badge--pending">{{ STAGE_LABEL[s.etapa] ?? s.etapa }}</span>
                            <span v-else class="badge badge--processing">{{ STAGE_LABEL[s.etapa] ?? s.etapa }}</span>
                            <p v-if="s.codigoenviocia" class="shipment-table__meta">Courier: {{ s.codigoenviocia }}</p>
                        </td>
                        <td>
                            <button
                                v-if="s.etapa === 'pending'"
                                type="button"
                                class="btn btn--small"
                                @click="advance(s, 'procesar')"
                            >
                                Procesar
                            </button>
                            <button
                                v-else-if="s.etapa === 'processing'"
                                type="button"
                                class="btn btn--small"
                                @click="advance(s, 'encargar')"
                            >
                                Entregar a courier
                            </button>
                            <button
                                v-else-if="s.etapa === 'shipped'"
                                type="button"
                                class="btn btn--small"
                                @click="advance(s, 'entregar')"
                            >
                                Marcar entregado
                            </button>
                            <span v-else-if="s.etapa === 'delivered'" class="shipment-table__meta">✓</span>
                        </td>
                    </tr>
                    <tr v-if="!shipments.length">
                        <td colspan="9" class="store-empty">No hay envíos en esta vista.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AdminLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '../AdminLayout.vue';
import { money } from '../../../components/format';

const STAGE_LABEL = {
    pending: 'Pendiente',
    processing: 'Procesado',
    shipped: 'Entregado al courier',
    delivered: 'Entregado',
};

const props = defineProps({
    shipments: { type: Array, default: () => [] },
    counts: { type: Object, required: true },
    status: { type: String, default: 'all' },
});

const form = useForm({ action: '', codigoenviocia: '' });

function advance(shipment, action) {
    const payload = { action };
    if (action === 'encargar') {
        const code = prompt('Código de seguimiento del courier (codigoenviocia):', form.codigoenviocia);
        if (code === null) return;
        payload.codigoenviocia = code;
    }
    form.transform(() => payload).post(`/admin/envios/${shipment.id}/avanzar`, {
        preserveScroll: true,
    });
}

const filters = computed(() => [
    ['all', 'Todos'],
    ['pending', `Pendientes (${props.counts.pending})`],
    ['processing', `Procesados (${props.counts.processing})`],
    ['delivered', `Entregados (${props.counts.delivered})`],
]);
</script>
