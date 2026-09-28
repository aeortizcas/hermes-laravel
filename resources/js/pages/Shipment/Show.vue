<template>
    <Layout>
        <Head :title="`Envío #${shipment.id}`" />
        <div class="container page">
            <h1 class="page__title">Envío #{{ shipment.id }}</h1>

            <div v-if="shipment.codigoenvio" class="store-flash">
                Código de seguimiento: <strong>{{ shipment.codigoenvio }}</strong>
            </div>

            <div class="shipment-card">
                <h2 class="shipment-card__title">Estado del envío</h2>
                <div class="shipment-timeline">
                    <div
                        v-for="step in steps"
                        :key="step.key"
                        class="shipment-timeline__step"
                        :class="step.done ? 'is-done' : ''"
                    >
                        <span class="shipment-timeline__dot">{{ step.done ? '✓' : step.index + 1 }}</span>
                        <div>
                            <strong>{{ step.label }}</strong>
                            <p class="shipment-card__meta">{{ step.detail }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="shipment-card">
                <div class="shipment-card__head">
                    <div>
                        <strong>{{ shipment.producto }}</strong>
                        <p class="shipment-card__meta">
                            {{ shipment.cantidad }} × {{ money(shipment.precio) }}
                        </p>
                    </div>
                </div>
                <dl class="shipment-totals">
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
                        <dd>{{ shipment.ciudad ?? 'Pendiente' }}</dd>
                    </div>
                    <div>
                        <dt>Fecha de solicitud</dt>
                        <dd>{{ shipment.fechasol }}</dd>
                    </div>
                    <div>
                        <dt>Subtotal</dt>
                        <dd>{{ money(shipment.subtotal) }}</dd>
                    </div>
                    <div>
                        <dt>IVA (15%)</dt>
                        <dd>{{ money(shipment.iva) }}</dd>
                    </div>
                    <div>
                        <dt>Envío</dt>
                        <dd>{{ money(shipment.precioenvio) }}</dd>
                    </div>
                    <div class="shipment-totals__total">
                        <dt>Total</dt>
                        <dd>{{ money(shipment.total) }}</dd>
                    </div>
                </dl>
            </div>

            <p>
                <Link :href="`/mis-envios/${shipment.id}/factura`" class="btn btn--primary">
                    Ver factura imprimible
                </Link>
            </p>

            <div v-if="corresponsal" class="shipment-card">
                <h2>Corresponsal</h2>
                <dl class="shipment-totals">
                    <div>
                        <dt>Nombre</dt>
                        <dd>{{ corresponsal.nombre }}</dd>
                    </div>
                    <div>
                        <dt>Dirección</dt>
                        <dd>{{ corresponsal.Direccion }}</dd>
                    </div>
                    <div>
                        <dt>Teléfono</dt>
                        <dd>{{ corresponsal.telefono }}</dd>
                    </div>
                    <div>
                        <dt>Correo</dt>
                        <dd>{{ corresponsal.email }}</dd>
                    </div>
                </dl>
            </div>

            <p>
                <Link href="/mis-envios">← Mis envíos</Link>
            </p>
        </div>
    </Layout>
</template>

<script setup>
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import Layout from '../../components/Layout.vue';
import { money } from '../../components/format';

const STEPS = [
    ['solicitud', 'Solicitud', 'fechasol'],
    ['confirmado', 'Confirmado', 'codigoenvio'],
    ['procesado', 'Procesado', 'fechapro'],
    ['encargo', 'Entregado al courier', 'fechaencargo'],
    ['entrega', 'Entregado', 'fechaentrega'],
];

const props = defineProps({
    shipment: { type: Object, required: true },
    corresponsal: { type: Object, default: null },
});

const steps = computed(() =>
    STEPS.map(([key, label, field], i) => {
        const value = props.shipment?.[field];
        const done = !!value;
        const detail =
            field === 'codigoenvio' ? (done ? value : 'Pendiente') : (value ?? 'Pendiente');

        return { key, label, field, value, done, index: i, detail };
    })
);
</script>
