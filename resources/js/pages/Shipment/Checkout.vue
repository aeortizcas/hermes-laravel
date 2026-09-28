<template>
    <Layout>
        <Head title="Tu pedido" />
        <div class="container page">
            <h1 class="page__title">Tu pedido</h1>
            <p class="page__lead">
                Elige la empresa de envío y el método de pago para cada producto, igual que en la
                tienda anterior.
            </p>

            <form class="shipment-form" @submit.prevent="submit">
                <div v-for="row in rows" :key="row.line.product.id" class="shipment-card">
                    <div class="shipment-card__head">
                        <div>
                            <strong>{{ row.line.qty }} × {{ row.line.product.name }}</strong>
                            <p class="shipment-card__meta">
                                {{ money(row.line.product.price) }} c/u · subtotal {{ money(row.line.subtotal) }}
                            </p>
                        </div>
                        <p v-if="form.errors[`shipping.${row.line.product.id}.cia`]" class="form-error">
                            {{ form.errors[`shipping.${row.line.product.id}.cia`] }}
                        </p>
                    </div>
                    <div class="shipment-card__opts">
                        <div>
                            <span class="shipment-card__label">Empresa de envío *</span>
                            <label v-for="c in companies" :key="c.id" class="radio">
                                <input
                                    type="radio"
                                    :name="`cia-${row.line.product.id}`"
                                    :checked="row.opts.cia === String(c.id)"
                                    @change="setOpt(row.line.product.id, 'cia', String(c.id))"
                                />
                                {{ c.name }}
                            </label>
                        </div>
                        <div>
                            <span class="shipment-card__label">Método de pago *</span>
                            <label v-for="m in methods" :key="m.id" class="radio">
                                <input
                                    type="radio"
                                    :name="`pago-${row.line.product.id}`"
                                    :checked="row.opts.pago === String(m.id)"
                                    @change="setOpt(row.line.product.id, 'pago', String(m.id))"
                                />
                                {{ m.name }}
                            </label>
                        </div>
                    </div>
                </div>

                <p v-if="form.errors.shipping" class="form-error">{{ form.errors.shipping }}</p>

                <aside class="cart-summary">
                    <p>
                        Subtotal: <strong>{{ money(totals.subtotal) }}</strong>
                    </p>
                    <p>
                        IVA ({{ totals.iva_rate }}%): <strong>{{ money(totals.iva) }}</strong>
                    </p>
                    <p class="cart-summary__total">
                        Total (sin envío): <strong>{{ money(totals.total) }}</strong>
                    </p>
                    <button type="submit" class="btn btn--primary" :disabled="form.processing">
                        {{ form.processing ? 'Registrando…' : 'Registrar pedido' }}
                    </button>
                    <p class="checkout__note">
                        El costo de envío se calcula al elegir el destino en el siguiente paso.
                    </p>
                    <p>
                        <Link href="/carrito">← Volver al carrito</Link>
                    </p>
                </aside>
            </form>
        </div>
    </Layout>
</template>

<script setup>
import { computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import Layout from '../../components/Layout.vue';
import { money } from '../../components/format';

const props = defineProps({
    lines: { type: Array, default: () => [] },
    totals: { type: Object, default: () => ({}) },
    companies: { type: Array, default: () => [] },
    methods: { type: Array, default: () => [] },
});

const initial = {};
props.lines.forEach((line) => {
    initial[line.product.id] = { cia: '', pago: '' };
});

const form = useForm({ shipping: initial });

const rows = computed(() =>
    props.lines.map((line) => ({
        line,
        opts: form.shipping?.[line.product.id] ?? { cia: '', pago: '' },
    }))
);

function setOpt(productId, key, value) {
    form.shipping = {
        ...form.shipping,
        [productId]: { ...form.shipping[productId], [key]: value },
    };
}

function submit() {
    form.post('/pedido');
}
</script>
