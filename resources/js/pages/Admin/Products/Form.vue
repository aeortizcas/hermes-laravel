<template>
    <AdminLayout :title="isEdit ? 'Editar producto' : 'Nuevo producto'">
        <Head :title="isEdit ? 'Editar producto' : 'Nuevo producto'" />
        <div class="admin__content">
            <h1 class="page__title">{{ isEdit ? `Editar: ${product.name}` : 'Nuevo producto' }}</h1>

            <form class="form admin-form" @submit.prevent="submit">
                <label>
                    Nombre *
                    <input
                        type="text"
                        :value="form.name"
                        @change="e => form.name = e.target.value"
                    />
                    <p v-if="form.errors.name" class="form-error">{{ form.errors.name }}</p>
                </label>
                <label>
                    Descripción
                    <textarea
                        rows="4"
                        :value="form.description"
                        @change="e => form.description = e.target.value"
                    />
                </label>
                <div class="form__row">
                    <label>
                        Categoría
                        <select v-model="form.category_id">
                            <option value="">— Sin categoría —</option>
                            <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
                        </select>
                    </label>
                    <label>
                        Unidad
                        <input type="text" :value="form.unit" @change="e => form.unit = e.target.value" />
                    </label>
                </div>
                <div class="form__row">
                    <label>
                        Código
                        <input type="text" :value="form.code" @change="e => form.code = e.target.value" />
                    </label>
                    <label>
                        Estado
                        <select v-model="form.estado">
                            <option :value="1">Activo</option>
                            <option :value="0">Inactivo</option>
                        </select>
                    </label>
                </div>
                <div class="form__row">
                    <label>
                        Precio (USD) *
                        <input
                            type="number"
                            step="0.01"
                            min="0"
                            :value="form.price"
                            @change="e => form.price = e.target.value"
                        />
                        <p v-if="form.errors.price" class="form-error">{{ form.errors.price }}</p>
                    </label>
                    <label>
                        Stock
                        <input
                            type="number"
                            min="0"
                            :value="form.stock"
                            @change="e => form.stock = e.target.value"
                        />
                        <p v-if="form.errors.stock" class="form-error">{{ form.errors.stock }}</p>
                    </label>
                </div>
                <label>
                    Imagen de portada (nueva)
                    <input
                        type="file"
                        accept="image/*"
                        @change="e => form.cover = e.target.files[0]"
                    />
                    <p v-if="form.errors.cover" class="form-error">{{ form.errors.cover }}</p>
                </label>

                <div v-if="product?.images?.length > 0" class="admin-gallery">
                    <img v-for="img in product.images" :key="img.id" :src="img.url" alt="" />
                </div>

                <div class="admin-form__actions">
                    <button type="submit" class="btn btn--primary" :disabled="form.processing">
                        <template v-if="form.processing">Guardando…</template>
                        <template v-else-if="isEdit">Guardar cambios</template>
                        <template v-else>Crear producto</template>
                    </button>
                    <Link href="/admin/productos" class="btn">Cancelar</Link>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '../AdminLayout.vue';

const props = defineProps({
    product: { type: Object, default: null },
    categories: { type: Array, default: () => [] },
});

const isEdit = props.product !== null;

const form = useForm({
    name: props.product?.name ?? '',
    description: props.product?.description ?? '',
    category_id: props.product?.category_id ?? '',
    unit: props.product?.unit ?? '',
    code: props.product?.code ?? '',
    price: props.product?.price ?? '',
    stock: props.product?.stock ?? '',
    estado: props.product?.estado ?? 1,
    cover: null,
});

function submit() {
    if (isEdit) {
        form.put(`/admin/productos/${props.product.id}`);
    } else {
        form.post('/admin/productos');
    }
}
</script>
