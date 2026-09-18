import { Head, Link, router, useForm } from '@inertiajs/react';
import { useState } from 'react';
import AdminLayout from '../AdminLayout';

export default function PagesIndex({ pages }) {
    const [editing, setEditing] = useState(null);
    const form = useForm({ name: '', description: '' });

    function startEdit(page) {
        form.setData({ name: page.name, description: page.description });
        setEditing(page.id);
    }

    function cancelEdit() {
        form.reset();
        setEditing(null);
    }

    function submit(e) {
        e.preventDefault();
        if (editing !== null) {
            form.put(`/admin/paginas/${editing}`, {
                preserveScroll: true,
                onSuccess: () => {
                    form.reset();
                    setEditing(null);
                },
            });
        } else {
            form.post('/admin/paginas', { preserveScroll: true, onSuccess: () => form.reset() });
        }
    }

    function remove(page) {
        if (confirm(`¿Eliminar la página “${page.name}”?`)) {
            router.delete(`/admin/paginas/${page.id}`);
        }
    }

    return (
        <AdminLayout title="Páginas del sistema">
            <Head title="Páginas" />
            <div className="admin__content">
                <h1 className="page__title">Páginas del sistema</h1>

                <form className="form admin-form admin-form--row" onSubmit={submit}>
                    <label>
                        Nombre
                        <input type="text" value={form.data.name} onChange={(e) => form.setData('name', e.target.value)} />
                        {form.errors.name && <p className="form-error">{form.errors.name}</p>}
                    </label>
                    <label>
                        Descripción
                        <input
                            type="text"
                            value={form.data.description}
                            onChange={(e) => form.setData('description', e.target.value)}
                        />
                    </label>
                    <button type="submit" className="btn btn--primary" disabled={form.processing}>
                        {form.processing ? 'Guardando…' : editing !== null ? 'Guardar cambios' : 'Crear página'}
                    </button>
                    {editing !== null && (
                        <button type="button" className="btn" onClick={cancelEdit}>
                            Cancelar
                        </button>
                    )}
                </form>

                <table className="cart-table admin-catalog">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        {pages.map((page) => (
                            <tr key={page.id}>
                                <td>{page.id}</td>
                                <td>{page.name}</td>
                                <td>{page.description || '—'}</td>
                                <td className="admin-catalog__actions">
                                    <button type="button" className="admin__edit" onClick={() => startEdit(page)}>
                                        Editar
                                    </button>
                                    <button type="button" className="admin__danger" onClick={() => remove(page)}>
                                        Eliminar
                                    </button>
                                </td>
                            </tr>
                        ))}
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
    );
}