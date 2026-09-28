<template>
    <Layout>
        <Head title="Confirmar envío" />
        <div class="container page">
            <h1 class="page__title">Confirmar envío #{{ shipment.id }}</h1>

            <div class="checkout__grid">
                <div class="shipment-card">
                    <div class="shipment-card__head">
                        <div>
                            <strong>{{ shipment.producto }}</strong>
                            <p class="shipment-card__meta">
                                {{ shipment.cantidad }} × {{ money(shipment.precio) }} · {{ shipment.empresa }} ·{{ ' ' }}
                                {{ shipment.metodoPago }}
                            </p>
                        </div>
                    </div>
                    <dl class="shipment-totals">
                        <div>
                            <dt>Subtotal</dt>
                            <dd>{{ money(shipment.subtotal) }}</dd>
                        </div>
                        <div>
                            <dt>IVA (15%)</dt>
                            <dd>{{ money(shipment.iva) }}</dd>
                        </div>
                        <div>
                            <dt>Envío a {{ shipment.ciudad }}</dt>
                            <dd>{{ money(shipment.precioenvio) }}</dd>
                        </div>
                        <div class="shipment-totals__total">
                            <dt>Total</dt>
                            <dd>{{ money(shipment.total) }}</dd>
                        </div>
                    </dl>
                </div>

                <form class="form" @submit.prevent="submit">
                    <h2>Datos del corresponsal</h2>
                    <label>
                        Nombre completo *
                        <input
                            type="text"
                            :value="form.name"
                            @change="form.name = $event.target.value"
                        />
                        <p v-if="form.errors.name" class="form-error">{{ form.errors.name }}</p>
                    </label>
                    <label>
                        Dirección *
                        <input
                            type="text"
                            :value="form.direccion"
                            @change="form.direccion = $event.target.value"
                        />
                        <p v-if="form.errors.direccion" class="form-error">{{ form.errors.direccion }}</p>
                    </label>
                    <div class="form__row">
                        <label>
                            Teléfono *
                            <input
                                type="tel"
                                :value="form.telefono"
                                @change="form.telefono = $event.target.value"
                            />
                            <p v-if="form.errors.telefono" class="form-error">{{ form.errors.telefono }}</p>
                        </label>
                        <label>
                            Correo *
                            <input
                                type="email"
                                :value="form.email"
                                @change="form.email = $event.target.value"
                            />
                            <p v-if="form.errors.email" class="form-error">{{ form.errors.email }}</p>
                        </label>
                    </div>
                    <template v-if="shipment.metodoPago?.toLowerCase().includes('transferencia')">
                        <label>
                            Banco
                            <select :value="form.banco" @change="form.banco = $event.target.value">
                                <option value="">— Selecciona —</option>
                                <option v-for="b in banks" :key="b.id" :value="b.id">
                                    {{ b.name }}
                                </option>
                            </select>
                        </label>
                        <label>
                            Número de cuenta
                            <input
                                type="text"
                                :value="form.numero"
                                @change="form.numero = $event.target.value"
                            />
                        </label>
                    </template>
                    <button type="submit" class="btn btn--primary" :disabled="form.processing">
                        {{ form.processing ? 'Confirmando…' : 'Confirmar envío' }}
                    </button>
                    <p>
                        <Link href="/mis-envios">← Mis envíos</Link>
                    </p>
                </form>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import Layout from '../../components/Layout.vue';
import { money } from '../../components/format';

const props = defineProps({
    shipment: { type: Object, required: true },
    banks: { type: Array, default: () => [] },
});

const form = useForm({
    name: '',
    direccion: '',
    telefono: '',
    email: '',
    banco: '',
    numero: '',
});

function submit() {
    form.post(`/mis-envios/${props.shipment.id}/confirmar`);
}
</script>
