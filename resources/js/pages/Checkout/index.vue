<template>
    <Layout>
        <Head title="Finalizar compra" />
        <div class="container page checkout">
            <h1 class="page__title">Finalizar compra</h1>

            <div class="checkout__grid">
                <form class="form" @submit.prevent="submit">
                    <h2>Tus datos</h2>
                    <label>Nombre *
                        <input
                            type="text"
                            :value="form.name"
                            @change="e => form.name = e.target.value"
                        />
                        <p v-if="form.errors.name" class="form-error">{{ form.errors.name }}</p>
                    </label>
                    <label>Correo electrónico *
                        <input
                            type="email"
                            :value="form.email"
                            @change="e => form.email = e.target.value"
                        />
                        <p v-if="form.errors.email" class="form-error">{{ form.errors.email }}</p>
                    </label>
                    <label>Teléfono
                        <input
                            type="tel"
                            :value="form.telefono"
                            @change="e => form.telefono = e.target.value"
                        />
                        <p v-if="form.errors.telefono" class="form-error">{{ form.errors.telefono }}</p>
                    </label>
                    <label>Dirección
                        <input
                            type="text"
                            :value="form.direccion"
                            @change="e => form.direccion = e.target.value"
                        />
                        <p v-if="form.errors.direccion" class="form-error">{{ form.errors.direccion }}</p>
                    </label>

                    <button type="submit" class="btn btn--primary" :disabled="form.processing">
                        {{ form.processing ? 'Procesando…' : 'Confirmar pedido' }}
                    </button>
                    <p class="checkout__note">El pago (PayPal / transferencia) se habilitará en la fase 2 de la
                        migración; por ahora el pedido queda guardado como pendiente.</p>
                </form>

                <aside class="checkout__summary">
                    <h2>Resumen</h2>
                    <div v-for="line in lines" :key="line.product.id" class="checkout__line">
                        <span>{{ line.qty }} × {{ line.product.name }}</span>
                        <strong>{{ money(line.subtotal) }}</strong>
                    </div>
                    <hr />
                    <p>Subtotal <strong>{{ money(totals.subtotal) }}</strong></p>
                    <p>IVA ({{ totals.iva_rate }}%) <strong>{{ money(totals.iva) }}</strong></p>
                    <p class="checkout__total">Total <strong>{{ money(totals.total) }}</strong></p>
                </aside>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import Layout from '../../components/Layout.vue';
import { money } from '../../components/format';

defineProps({
    lines: { type: Array, default: () => [] },
    totals: { type: Object, required: true },
});

const form = useForm({ name: '', email: '', telefono: '', direccion: '' });

function submit() {
    form.post('/checkout');
}
</script>
