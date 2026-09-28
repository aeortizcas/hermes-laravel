<template>
    <div class="admin-login">
        <Head title="Acceso admin" />
        <form class="form admin-login__card" @submit.prevent="submit">
            <h1>Acceso de administración</h1>
            <label>
                Usuario
                <input
                    type="text"
                    :value="form.usuario"
                    @change="e => form.usuario = e.target.value"
                    autocomplete="username"
                />
                <p v-if="form.errors.usuario" class="form-error">{{ form.errors.usuario }}</p>
            </label>
            <label>
                Contraseña
                <input
                    type="password"
                    :value="form.password"
                    @change="e => form.password = e.target.value"
                    autocomplete="current-password"
                />
            </label>
            <button type="submit" class="btn btn--primary" :disabled="form.processing">
                {{ form.processing ? 'Entrando…' : 'Entrar' }}
            </button>
        </form>
    </div>
</template>

<script setup>
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({ usuario: '', password: '' });

function submit() {
    form.post('/admin/login');
}
</script>
