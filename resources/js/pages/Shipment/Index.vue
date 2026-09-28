<template>
    <Layout>
        <Head title="Mis envíos" />
        <div class="container page">
            <h1 class="page__title">Mis envíos</h1>
            <p class="page__lead">
                Aquí aparecen tus pedidos con envío (tabla tbl_rastreo del sistema anterior).
            </p>

            <p v-if="!shipments.length" class="store-empty">Todavía no tienes envíos registrados.</p>
            <table v-else class="cart-table shipment-table">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Empresa</th>
                        <th>Pago</th>
                        <th>Precio</th>
                        <th>Total</th>
                        <th>Destino</th>
                        <th>Estado</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="shipment in shipments" :key="shipment.id">
                        <td>
                            <strong>{{ shipment.producto }}</strong>
                            <p class="shipment-table__meta">N° {{ shipment.id }} · {{ shipment.cantidad }} unidad(es)</p>
                        </td>
                        <td>{{ shipment.empresa }}</td>
                        <td>{{ shipment.metodoPago }}</td>
                        <td>{{ money(shipment.precio) }}</td>
                        <td>{{ money(shipment.total) }}</td>
                        <td>
                            <form
                                v-if="shipment.pendienteDestino"
                                class="shipment-destino"
                                @submit.prevent="pickCity(shipment)"
                            >
                                <select
                                    v-model="selections[shipment.id]"
                                    :aria-label="`Destino de ${shipment.producto}`"
                                >
                                    <option value="">— Ciudad —</option>
                                    <option v-for="c in cities" :key="c.id" :value="c.id">
                                        {{ c.name }}
                                    </option>
                                </select>
                                <button
                                    type="submit"
                                    class="btn btn--small"
                                    :disabled="processingId === shipment.id"
                                >
                                    Elegir
                                </button>
                            </form>
                            <template v-else>
                                <span class="shipment-table__dest">{{ shipment.ciudad }}</span>
                                <p v-if="shipment.corresponsal?.direccion" class="shipment-table__meta">
                                    📍 {{ shipment.corresponsal.direccion }}
                                </p>
                                <p class="shipment-table__meta">Envío: {{ money(shipment.precioenvio) }}</p>
                            </template>
                        </td>
                        <td>
                            <span v-if="shipment.pendienteDestino" class="badge badge--pending">
                                Paso 1: destino
                            </span>
                            <span v-else-if="shipment.codigoenvio" class="badge badge--done">
                                Confirmado
                            </span>
                            <Link
                                v-else
                                :href="`/mis-envios/${shipment.id}/confirmar`"
                                class="btn btn--small"
                            >
                                Confirmar
                            </Link>
                        </td>
                        <td>
                            <Link :href="`/mis-envios/${shipment.id}`">Ver</Link>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </Layout>
</template>

<script setup>
import { reactive, ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import Layout from '../../components/Layout.vue';
import { money } from '../../components/format';

const props = defineProps({
    shipments: { type: Array, default: () => [] },
    cities: { type: Array, default: () => [] },
});

const selections = reactive(
    Object.fromEntries(props.shipments.map((shipment) => [shipment.id, shipment.ciudadId ?? '']))
);

const processingId = ref(null);

function pickCity(shipment) {
    processingId.value = shipment.id;

    router.post(
        `/mis-envios/${shipment.id}/destino`,
        { city: selections[shipment.id] ?? '' },
        {
            preserveScroll: true,
            onFinish: () => {
                processingId.value = null;
            },
        }
    );
}
</script>
