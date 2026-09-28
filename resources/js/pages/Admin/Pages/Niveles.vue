<template>
    <AdminLayout title="Niveles (permisos)">
        <Head title="Niveles" />
        <div class="admin__content">
            <h1 class="page__title">Niveles — permisos de usuario por página</h1>

            <form class="form admin-form admin-form--row admin-form--wrap" @submit.prevent="submit">
                <label class="admin-form__field">
                    Usuario
                    <select v-model="form.user_id">
                        <option value="">— Seleccionar —</option>
                        <option v-for="u in users" :key="u.value" :value="u.value">{{ u.label }}</option>
                    </select>
                    <p v-if="form.errors.user_id" class="form-error">{{ form.errors.user_id }}</p>
                </label>
                <label class="admin-form__field">
                    Página
                    <select v-model="form.page_id">
                        <option value="">— Seleccionar —</option>
                        <option v-for="p in pages" :key="p.value" :value="p.value">{{ p.label }}</option>
                    </select>
                    <p v-if="form.errors.page_id" class="form-error">{{ form.errors.page_id }}</p>
                </label>
                <label class="admin-form__field">
                    Acceso
                    <select v-model="form.access_type">
                        <option value="allow">Permitir</option>
                        <option value="deny">Denegar</option>
                    </select>
                </label>
                <div class="admin-form__actions">
                    <button type="submit" class="btn btn--primary" :disabled="form.processing">
                        {{ form.processing ? 'Guardando…' : 'Asignar permiso' }}
                    </button>
                </div>
            </form>

            <table class="cart-table admin-catalog">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Usuario</th>
                        <th>Página</th>
                        <th>Acceso</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="perm in permissions" :key="perm.id">
                        <td>{{ perm.id }}</td>
                        <td>{{ perm.user_name }}</td>
                        <td>{{ perm.page_name }}</td>
                        <td>
                            <span v-if="perm.access_type === 'allow'" class="badge badge--done">Permitir</span>
                            <span v-else class="badge badge--pending">Denegar</span>
                        </td>
                        <td>
                            <button type="button" class="admin__danger" @click="remove(perm)">
                                Quitar
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>

            <p>
                <Link href="/admin/paginas">← Páginas</Link> · <Link href="/admin">Dashboard</Link>
            </p>
        </div>
    </AdminLayout>
</template>

<script setup>
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AdminLayout from '../AdminLayout.vue';

defineProps({
    permissions: { type: Array, default: () => [] },
    users: { type: Array, default: () => [] },
    pages: { type: Array, default: () => [] },
});

const form = useForm({ user_id: '', page_id: '', access_type: 'allow' });

function submit() {
    form.post('/admin/paginas/niveles', {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
}

function remove(perm) {
    if (confirm(`¿Quitar el permiso de “${perm.user_name}” sobre “${perm.page_name}”?`)) {
        router.delete(`/admin/paginas/niveles/${perm.id}`);
    }
}
</script>
