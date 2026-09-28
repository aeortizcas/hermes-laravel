<template>
    <AdminLayout :title="title">
        <Head :title="title" />
        <div class="admin__content">
            <h1 class="page__title">{{ title }}</h1>

            <form
                v-if="!readonly"
                class="form admin-form admin-form--row admin-form--wrap"
                @submit.prevent="submit"
            >
                <label v-for="f in fields" :key="f.key" class="admin-form__field">
                    {{ f.label }}
                    <select v-if="f.type === 'select'" v-model="form[f.key]">
                        <option value="">— Seleccionar —</option>
                        <option v-for="o in f.options || []" :key="o.value" :value="o.value">
                            {{ o.label }}
                        </option>
                    </select>
                    <input
                        v-else-if="f.type === 'number'"
                        type="number"
                        step="any"
                        :value="form[f.key] ?? ''"
                        @change="e => form[f.key] = e.target.value"
                    />
                    <input
                        v-else
                        :type="f.type === 'date' ? 'date' : 'text'"
                        :value="form[f.key] ?? ''"
                        @change="e => form[f.key] = e.target.value"
                    />
                    <p v-if="form.errors[f.key]" class="form-error">{{ form.errors[f.key] }}</p>
                </label>
                <div class="admin-form__actions">
                    <button type="submit" class="btn btn--primary" :disabled="form.processing">
                        <template v-if="form.processing">Guardando…</template>
                        <template v-else-if="editing !== null">Guardar cambios</template>
                        <template v-else>Agregar</template>
                    </button>
                    <button
                        v-if="editing !== null"
                        type="button"
                        class="btn"
                        @click="cancelEdit"
                    >
                        Cancelar
                    </button>
                </div>
            </form>

            <table class="cart-table admin-catalog">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th v-for="f in fields" :key="f.key">{{ f.label }}</th>
                        <th>Estado</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="item in items" :key="item.id">
                        <td>{{ item.id }}</td>
                        <td v-for="f in fields" :key="f.key">{{ fieldValue(item, f.key) }}</td>
                        <td>
                            <template v-if="item.estado !== undefined">
                                <span v-if="item.estado !== 0" class="badge badge--done">Activo</span>
                                <span v-else class="badge badge--pending">Inactivo</span>
                            </template>
                            <template v-else>
                                <span class="badge badge--done">—</span>
                            </template>
                        </td>
                        <td class="admin-catalog__actions">
                            <button
                                v-if="!readonly"
                                type="button"
                                class="admin__edit"
                                @click="startEdit(item)"
                            >
                                Editar
                            </button>
                            <button
                                v-if="item.estado === undefined || item.estado !== 0"
                                type="button"
                                class="admin__danger"
                                @click="toggle(item)"
                            >
                                {{ item.estado === undefined ? 'Eliminar' : 'Desactivar' }}
                            </button>
                            <span v-else class="badge badge--pending">Inactivo</span>
                        </td>
                    </tr>
                </tbody>
            </table>

            <p>
                <Link href="/admin">← Dashboard</Link>
            </p>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref, watch } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AdminLayout from '../AdminLayout.vue';

const EMPTY = {};

const props = defineProps({
    entity: { type: String, required: true },
    title: { type: String, default: '' },
    fields: { type: Array, default: () => [] },
    items: { type: Array, default: () => [] },
    readonly: { type: Boolean, default: false },
});

const editing = ref(null);

const initial = {};
props.fields.forEach((f) => (initial[f.key] = ''));

const form = useForm(initial);

watch(
    () => props.entity,
    () => {
        Object.assign(form, initial);
        editing.value = null;
    }
);

function startEdit(item) {
    const data = {};
    props.fields.forEach((f) => (data[f.key] = item[f.key] ?? ''));
    Object.assign(form, data);
    editing.value = item.id;
}

function cancelEdit() {
    form.reset();
    editing.value = null;
}

function submit() {
    if (editing.value !== null) {
        form.put(`/admin/catalogos/${props.entity}/${editing.value}`, {
            preserveScroll: true,
            onSuccess: () => {
                form.reset();
                editing.value = null;
            },
        });
    } else {
        form.post(`/admin/catalogos/${props.entity}`, {
            preserveScroll: true,
            onSuccess: () => form.reset(),
        });
    }
}

function toggle(item) {
    if (confirm(`¿Desactivar/eliminar el elemento #${item.id}?`)) {
        router.delete(`/admin/catalogos/${props.entity}/${item.id}`);
    }
}

function fieldValue(item, key) {
    const v = item[key];
    if (v === null || v === undefined || v === '') return '—';
    return v;
}
</script>
