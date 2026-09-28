<template>
    <Layout>
        <Head title="Crear cuenta" />
        <div class="container page auth">
            <h1 class="page__title">Crear cuenta</h1>
            <form class="form" @submit.prevent="submit">
                <label>Ciudad
                    <select :value="form.ciudad" @change="e => form.ciudad = e.target.value">
                        <option value="">— Selecciona —</option>
                        <option v-for="c in cities" :key="c.id" :value="c.id">{{ c.name }}</option>
                    </select>
                </label>
                <label>Usuario *
                    <input
                        type="text"
                        :value="form.usuario"
                        @change="e => form.usuario = e.target.value"
                        autocomplete="username"
                    />
                    <p v-if="form.errors.usuario" class="form-error">{{ form.errors.usuario }}</p>
                </label>
                <label>Correo electrónico *
                    <input
                        type="email"
                        :value="form.email"
                        @change="e => form.email = e.target.value"
                        autocomplete="email"
                    />
                    <p v-if="form.errors.email" class="form-error">{{ form.errors.email }}</p>
                </label>
                <div class="form__row">
                    <label>Primer nombre *
                        <input
                            type="text"
                            :value="form.primerNombre"
                            @change="e => form.primerNombre = e.target.value"
                        />
                        <p v-if="form.errors.primerNombre" class="form-error">{{ form.errors.primerNombre }}</p>
                    </label>
                    <label>Segundo nombre
                        <input
                            type="text"
                            :value="form.segundoNombre"
                            @change="e => form.segundoNombre = e.target.value"
                        />
                    </label>
                </div>
                <div class="form__row">
                    <label>Primer apellido *
                        <input
                            type="text"
                            :value="form.primerApellido"
                            @change="e => form.primerApellido = e.target.value"
                        />
                        <p v-if="form.errors.primerApellido" class="form-error">{{ form.errors.primerApellido }}</p>
                    </label>
                    <label>Segundo apellido
                        <input
                            type="text"
                            :value="form.segundoApellido"
                            @change="e => form.segundoApellido = e.target.value"
                        />
                    </label>
                </div>
                <div class="form__row">
                    <label>Teléfono *
                        <input
                            type="tel"
                            :value="form.telefono"
                            @change="e => form.telefono = e.target.value"
                        />
                        <p v-if="form.errors.telefono" class="form-error">{{ form.errors.telefono }}</p>
                    </label>
                    <label>Dirección *
                        <input
                            type="text"
                            :value="form.direccion"
                            @change="e => form.direccion = e.target.value"
                        />
                        <p v-if="form.errors.direccion" class="form-error">{{ form.errors.direccion }}</p>
                    </label>
                </div>
                <div class="form__row">
                    <label>Contraseña *
                        <input
                            type="password"
                            :value="form.password"
                            @change="e => form.password = e.target.value"
                            autocomplete="new-password"
                        />
                        <p v-if="form.errors.password" class="form-error">{{ form.errors.password }}</p>
                    </label>
                    <label>Repite la contraseña *
                        <input
                            type="password"
                            :value="form.password_confirmation"
                            @change="e => form.password_confirmation = e.target.value"
                            autocomplete="new-password"
                        />
                    </label>
                </div>
                <button type="submit" class="btn btn--primary" :disabled="form.processing">
                    {{ form.processing ? 'Creando…' : 'Registrarme' }}
                </button>
                <p class="auth__switch">
                    ¿Ya tienes cuenta? <Link href="/login">Inicia sesión</Link>
                </p>
            </form>
        </div>
    </Layout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import Layout from '../../components/Layout.vue';

defineProps({
    cities: { type: Array, default: () => [] },
});

const form = useForm({
    ciudad: '',
    usuario: '',
    email: '',
    primerNombre: '',
    segundoNombre: '',
    primerApellido: '',
    segundoApellido: '',
    telefono: '',
    direccion: '',
    password: '',
    password_confirmation: '',
});

function submit() {
    form.post('/registro');
}
</script>
