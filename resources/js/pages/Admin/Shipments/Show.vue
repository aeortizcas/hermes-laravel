<template>
    <AdminLayout :title="`Envío #${shipment.id}`">
        <Head :title="`Envío #${shipment.id}`" />
        <div class="admin__content">
            <div class="admin__head">
                <h1 class="page__title">Envío #{{ shipment.id }}</h1>
                <div class="admin__head-actions">
                    <Link :href="`/admin/envios/${shipment.id}/factura`" class="btn">Factura</Link>
                    <Link href="/admin/envios" class="btn">← Todos los envíos</Link>
                </div>
            </div>

            <div class="shipment-card">
                <dl class="shipment-totals">
                    <div>
                        <dt>Cliente</dt>
                        <dd>{{ shipment.cliente || '—' }} ({{ shipment.usuario }})</dd>
                    </div>
                    <div>
                        <dt>Producto</dt>
                        <dd>{{ shipment.producto }} × {{ shipment.cantidad }}</dd>
                    </div>
                    <div>
                        <dt>Empresa de envío</dt>
                        <dd>{{ shipment.empresa }}</dd>
                    </div>
                    <div>
                        <dt>Método de pago</dt>
                        <dd>{{ shipment.metodoPago }}</dd>
                    </div>
                    <div>
                        <dt>Destino</dt>
                        <dd>{{ shipment.ciudad }}</dd>
                    </div>
                    <div>
                        <dt>Subtotal / IVA / Envío</dt>
                        <dd>{{ money(shipment.subtotal) }} / {{ money(shipment.iva) }} / {{ money(shipment.precioenvio) }}</dd>
                    </div>
                    <div class="shipment-totals__total">
                        <dt>Total</dt>
                        <dd>{{ money(shipment.total) }}</dd>
                    </div>
                    <div>
                        <dt>Código interno</dt>
                        <dd>{{ shipment.codigoenvio ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt>Código del courier</dt>
                        <dd>
                            <span v-if="shipment.codigoenviocia">{{ shipment.codigoenviocia }}</span>
                            <em v-else class="shipment-card__meta">aún no asignado</em>
                        </dd>
                    </div>
                </dl>
            </div>

            <h2 class="admin-subtitle">Etapas</h2>
            <div class="shipment-timeline">
                <div
                    v-for="([key, label, field], i) in STEPS"
                    :key="key"
                    :class="`shipment-timeline__step ${shipment[field] !== null ? 'is-done' : ''}`"
                >
                    <span class="shipment-timeline__dot">{{ shipment[field] !== null ? '✓' : i + 1 }}</span>
                    <div>
                        <strong>{{ label }}</strong>
                        <p class="shipment-card__meta">{{ shipment[field] ?? 'Pendiente' }}</p>
                    </div>
                </div>
            </div>

            <div class="admin-form shipment-card" :style="{ maxWidth: '420px' }">
                <h2>Avanzar etapa</h2>
                <form v-if="shipment.etapa === 'pending'" class="admin-actions" @submit.prevent="advance('procesar')">
                    <button type="submit" class="btn btn--primary">
                        Marcar como procesado (fechapro)
                    </button>
                </form>
                <form
                    v-else-if="shipment.etapa === 'processing'"
                    class="admin-actions"
                    @submit.prevent="advance('encargar')"
                >
                    <label>
                        Código de seguimiento del courier (codigoenviocia)
                        <input
                            type="text"
                            :value="form.codigoenviocia"
                            @change="e => form.codigoenviocia = e.target.value"
                        />
                    </label>
                    <button type="submit" class="btn btn--primary">
                        Entregar a courier (fechaencargo)
                    </button>
                </form>
                <form
                    v-else-if="shipment.etapa === 'shipped'"
                    class="admin-actions"
                    @submit.prevent="advance('entregar')"
                >
                    <button type="submit" class="btn btn--primary">
                        Marcar entregado (fechaentrega)
                    </button>
                </form>
                <p v-else-if="shipment.etapa === 'delivered'" class="shipment-card__meta">
                    Este envío ya fue entregado.
                </p>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '../AdminLayout.vue';
import { money } from '../../../components/format';

const STEPS = [
    ['solicitud', 'Solicitud', 'fechasol'],
    ['procesado', 'Procesado', 'fechapro'],
    ['encargo', 'Entregado al courier', 'fechaencargo'],
    ['entrega', 'Entregado', 'fechaentrega'],
];

const props = defineProps({
    shipment: { type: Object, required: true },
});

const form = useForm({ action: '', codigoenviocia: props.shipment.codigoenviocia ?? '' });

function advance(action) {
    form
        .transform(() => ({
            action,
            codigoenviocia: form.codigoenviocia,
        }))
        .post(`/admin/envios/${props.shipment.id}/avanzar`);
}
</script>
