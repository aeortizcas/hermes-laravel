<template>
    <Layout>
        <Head title="Membresías" />
        <div class="store-container page">
            <h1 class="page__title">Elige tu membresía</h1>
            <p v-if="current" class="store-flash">
                Tu membresía actual: <strong>{{ current.membership_type === 'personal' ? 'Personal' : 'Empresarial' }}</strong>{{ currentNote }}
            </p>
            <p v-else>No tienes una membresía activa. Selecciona un plan para continuar.</p>

            <form @submit.prevent="submit">
                <div class="membership-options">
                    <label
                        v-for="plan in PLANS"
                        :key="plan.value"
                        class="membership-card"
                        :class="form.membership === plan.value ? 'membership-card--active' : ''"
                    >
                        <input
                            type="radio"
                            name="membership"
                            :value="plan.value"
                            :checked="form.membership === plan.value"
                            @change="form.membership = $event.target.value"
                        />
                        <h3>{{ plan.name }}</h3>
                        <ul>
                            <li v-for="f in plan.features" :key="f">✔ {{ f }}</li>
                        </ul>
                    </label>
                </div>
                <p v-if="form.errors.membership" class="form-error">{{ form.errors.membership }}</p>
                <button type="submit" class="btn btn--primary" :disabled="form.processing">
                    {{ form.processing ? 'Guardando…' : 'Suscribirse' }}
                </button>
            </form>

            <p>
                <Link href="/">← Volver a la tienda</Link>
            </p>
        </div>
    </Layout>
</template>

<script setup>
import { computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import Layout from '../../components/Layout.vue';

const PLANS = [
    {
        value: 'personal',
        name: 'Membresía Personal',
        features: ['Acceso a funciones básicas', 'Ideal para usuarios individuales', 'Precio accesible'],
    },
    {
        value: 'business',
        name: 'Membresía Empresarial',
        features: ['Herramientas avanzadas y análisis', 'Perfecto para pequeñas empresas', 'Soporte prioritario'],
    },
];

const props = defineProps({
    current: { type: Object, default: null },
});

const form = useForm({ membership: props.current?.membership_type ?? '' });

const currentNote = computed(() =>
    props.current?.end_date ? ` (vence el ${props.current.end_date.slice(0, 10)})` : ''
);

function submit() {
    form.post('/suscripcion', { preserveScroll: true });
}
</script>
