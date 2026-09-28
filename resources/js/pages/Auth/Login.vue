<template>
    <Layout>
        <Head title="Iniciar sesión" />
        <div class="container page auth">
            <h1 class="page__title">Iniciar sesión</h1>
            <form class="form" @submit.prevent="submit">
                <label>Usuario o correo *
                    <input
                        type="text"
                        :value="form.usuario"
                        @change="e => form.usuario = e.target.value"
                        autocomplete="username"
                    />
                    <p v-if="form.errors.usuario" class="form-error">{{ form.errors.usuario }}</p>
                </label>
                <label>Contraseña *
                    <input
                        type="password"
                        :value="form.password"
                        @change="e => form.password = e.target.value"
                        autocomplete="current-password"
                    />
                    <p v-if="form.errors.password" class="form-error">{{ form.errors.password }}</p>
                </label>
                <button type="submit" class="btn btn--primary" :disabled="form.processing">
                    {{ form.processing ? 'Entrando…' : 'Entrar' }}
                </button>
                <p class="auth__switch">¿No tienes cuenta? <Link href="/registro">Regístrate</Link></p>
            </form>
        </div>
    </Layout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import Layout from '../../components/Layout.vue';

const form = useForm({ usuario: '', password: '' });

function submit() {
    form.post('/login');
}
</script>
