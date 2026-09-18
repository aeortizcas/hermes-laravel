import { Head, Link, useForm } from '@inertiajs/react';
import AdminLayout from '../AdminLayout';

export default function ProductForm({ product, categories }) {
    const isEdit = product !== null;
    const form = useForm({
        name: product?.name ?? '',
        description: product?.description ?? '',
        category_id: product?.category_id ?? '',
        unit: product?.unit ?? '',
        code: product?.code ?? '',
        price: product?.price ?? '',
        stock: product?.stock ?? '',
        estado: product?.estado ?? 1,
        cover: null,
    });

    function submit(e) {
        e.preventDefault();
        if (isEdit) {
            form.put(`/admin/productos/${product.id}`);
        } else {
            form.post('/admin/productos');
        }
    }

    return (
        <AdminLayout title={isEdit ? 'Editar producto' : 'Nuevo producto'}>
            <Head title={isEdit ? 'Editar producto' : 'Nuevo producto'} />
            <div className="admin__content">
                <h1 className="page__title">{isEdit ? `Editar: ${product.name}` : 'Nuevo producto'}</h1>

                <form className="form admin-form" onSubmit={submit}>
                    <label>
                        Nombre *
                        <input
                            type="text"
                            value={form.data.name}
                            onChange={(e) => form.setData('name', e.target.value)}
                        />
                        {form.errors.name && <p className="form-error">{form.errors.name}</p>}
                    </label>
                    <label>
                        Descripción
                        <textarea
                            rows="4"
                            value={form.data.description}
                            onChange={(e) => form.setData('description', e.target.value)}
                        />
                    </label>
                    <div className="form__row">
                        <label>
                            Categoría
                            <select
                                value={form.data.category_id}
                                onChange={(e) => form.setData('category_id', e.target.value)}
                            >
                                <option value="">— Sin categoría —</option>
                                {categories.map((c) => (
                                    <option key={c.id} value={c.id}>
                                        {c.name}
                                    </option>
                                ))}
                            </select>
                        </label>
                        <label>
                            Unidad
                            <input
                                type="text"
                                value={form.data.unit}
                                onChange={(e) => form.setData('unit', e.target.value)}
                            />
                        </label>
                    </div>
                    <div className="form__row">
                        <label>
                            Código
                            <input
                                type="text"
                                value={form.data.code}
                                onChange={(e) => form.setData('code', e.target.value)}
                            />
                        </label>
                        <label>
                            Estado
                            <select
                                value={form.data.estado}
                                onChange={(e) => form.setData('estado', e.target.value)}
                            >
                                <option value={1}>Activo</option>
                                <option value={0}>Inactivo</option>
                            </select>
                        </label>
                    </div>
                    <div className="form__row">
                        <label>
                            Precio (USD) *
                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                value={form.data.price}
                                onChange={(e) => form.setData('price', e.target.value)}
                            />
                            {form.errors.price && <p className="form-error">{form.errors.price}</p>}
                        </label>
                        <label>
                            Stock
                            <input
                                type="number"
                                min="0"
                                value={form.data.stock}
                                onChange={(e) => form.setData('stock', e.target.value)}
                            />
                            {form.errors.stock && <p className="form-error">{form.errors.stock}</p>}
                        </label>
                    </div>
                    <label>
                        Imagen de portada (nueva)
                        <input
                            type="file"
                            accept="image/*"
                            onChange={(e) => form.setData('cover', e.target.files[0])}
                        />
                        {form.errors.cover && <p className="form-error">{form.errors.cover}</p>}
                    </label>

                    {product?.images?.length > 0 && (
                        <div className="admin-gallery">
                            {product.images.map((img) => (
                                <img key={img.id} src={img.url} alt="" />
                            ))}
                        </div>
                    )}

                    <div className="admin-form__actions">
                        <button type="submit" className="btn btn--primary" disabled={form.processing}>
                            {form.processing ? 'Guardando…' : isEdit ? 'Guardar cambios' : 'Crear producto'}
                        </button>
                        <Link href="/admin/productos" className="btn">
                            Cancelar
                        </Link>
                    </div>
                </form>
            </div>
        </AdminLayout>
    );
}