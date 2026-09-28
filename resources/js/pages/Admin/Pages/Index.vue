<template>
    <AdminLayout title="Páginas del sistema">
        <Head title="Páginas" />
        <div class="admin__content">
            <h1 class="page__title">Páginas del sistema</h1>

            <form class="form admin-form admin-form--row" @submit.prevent="submit">
                <label>
                    Nombre
                    <input type="text" :value="form.name" @change="e => form.name = e.target.value" />
                    <p v-if="form.errors.name" class="form-error">{{ form.errors.name }}</p>
                </label>
                <label>
                    Descripción
                    <input
                        type="text"
                        :value="form.description"
                        @change="e => form.description = e.target.value"
                    />
                </label>
                <button type="submit" class="btn btn--primary" :disabled="form.processing">
                    <template v-if="form.processing">Guardando…</template>
                    <template v-else-if="editing !== null">Guardar cambios</template>
                    <template v-else>Crear página</template>
                </button>
                <button v-if="editing !== null" type="button" class="btn" @click="cancelEdit">
                    Cancelar
                </button>
            </form>

            <table class="cart-table admin-catalog">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="item in pages" :key="item.id">
                        <td>{{ item.id }}</td>
                        <td>{{ item.name }}</td>
                        <td>{{ item.description || '—' }}</td>
                        <td class="admin-catalog__actions">
                            <button type="button" class="admin__edit" @click="startEdit(item)">
                                Editar
                            </button>
                            <button type="button" class="admin__danger" @click="remove(item)">
                                Eliminar
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>

            <p>
                <Link href="/admin/paginas/niveles">Asignar niveles (permisos por usuario) →</Link>
            </p>
            <p>
                <Link href="/admin">← Dashboard</Link>
            </p>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AdminLayout from '../AdminLayout.vue';

defineProps({
    pages: { type: Array, default: () => [] },
});

const editing = ref(null);
const form = useForm({ name: '', description: '' });

function startEdit(item) {
    Object.assign(form, { name: item.name, description: item.description });
    editing.value = item.id;
}

function cancelEdit() {
    form.reset();
    editing.value = null;
}

function submit() {
    if (editing.value !== null) {
        form.put(`/admin/paginas/${editing.value}`, {
            preserveScroll: true,
            onSuccess: () => {
                form.reset();
                editing.value = null;
            },
        });
    } else {
        form.post('/admin/paginas', { preserveScroll: true, onSuccess: () => form.reset() });
    }
}

function remove(item) {
    if (confirm(`¿Eliminar la página “${item.name}”?`)) {
        router.delete(`/admin/paginas/${item.id}`);
    }
}
</script>
