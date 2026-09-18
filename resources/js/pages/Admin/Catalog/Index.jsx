import { Head, Link, router, useForm } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import AdminLayout from '../AdminLayout';

const EMPTY = {};

export default function CatalogIndex({ entity, title, fields, items, readonly }) {
    const [editing, setEditing] = useState(null);

    const initial = {};
    fields.forEach((f) => (initial[f.key] = ''));

    const form = useForm(initial);

    useEffect(() => {
        form.setData(initial);
        setEditing(null);
    }, [entity]);

    function startEdit(item) {
        const data = {};
        fields.forEach((f) => (data[f.key] = item[f.key] ?? ''));
        form.setData(data);
        setEditing(item.id);
    }

    function cancelEdit() {
        form.reset();
        setEditing(null);
    }

    function submit(e) {
        e.preventDefault();
        if (editing !== null) {
            form.put(`/admin/catalogos/${entity}/${editing}`, {
                preserveScroll: true,
                onSuccess: () => {
                    form.reset();
                    setEditing(null);
                },
            });
        } else {
            form.post(`/admin/catalogos/${entity}`, {
                preserveScroll: true,
                onSuccess: () => form.reset(),
            });
        }
    }

    function toggle(item) {
        if (confirm(`¿Desactivar/eliminar el elemento #${item.id}?`)) {
            router.delete(`/admin/catalogos/${entity}/${item.id}`);
        }
    }

    function fieldValue(item, key) {
        const v = item[key];
        if (v === null || v === undefined || v === '') return '—';
        return v;
    }

    return (
        <AdminLayout title={title}>
            <Head title={title} />
            <div className="admin__content">
                <h1 className="page__title">{title}</h1>

                {!readonly && (
                    <form className="form admin-form admin-form--row admin-form--wrap" onSubmit={submit}>
                        {fields.map((f) => (
                            <label key={f.key} className="admin-form__field">
                                {f.label}
                                {f.type === 'select' ? (
                                    <select value={form.data[f.key] ?? ''} onChange={(e) => form.setData(f.key, e.target.value)}>
                                        <option value="">— Seleccionar —</option>
                                        {(f.options || []).map((o) => (
                                            <option key={o.value} value={o.value}>
                                                {o.label}
                                            </option>
                                        ))}
                                    </select>
                                ) : f.type === 'number' ? (
                                    <input
                                        type="number"
                                        step="any"
                                        value={form.data[f.key] ?? ''}
                                        onChange={(e) => form.setData(f.key, e.target.value)}
                                    />
                                ) : (
                                    <input
                                        type={f.type === 'date' ? 'date' : 'text'}
                                        value={form.data[f.key] ?? ''}
                                        onChange={(e) => form.setData(f.key, e.target.value)}
                                    />
                                )}
                                {form.errors[f.key] && <p className="form-error">{form.errors[f.key]}</p>}
                            </label>
                        ))}
                        <div className="admin-form__actions">
                            <button type="submit" className="btn btn--primary" disabled={form.processing}>
                                {form.processing ? 'Guardando…' : editing !== null ? 'Guardar cambios' : 'Agregar'}
                            </button>
                            {editing !== null && (
                                <button type="button" className="btn" onClick={cancelEdit}>
                                    Cancelar
                                </button>
                            )}
                        </div>
                    </form>
                )}

                <table className="cart-table admin-catalog">
                    <thead>
                        <tr>
                            <th>ID</th>
                            {fields.map((f) => (
                                <th key={f.key}>{f.label}</th>
                            ))}
                            <th>Estado</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        {items.map((item) => (
                            <tr key={item.id}>
                                <td>{item.id}</td>
                                {fields.map((f) => (
                                    <td key={f.key}>{fieldValue(item, f.key)}</td>
                                ))}
                                <td>
                                    {item.estado !== undefined ? (
                                        item.estado !== 0 ? (
                                            <span className="badge badge--done">Activo</span>
                                        ) : (
                                            <span className="badge badge--pending">Inactivo</span>
                                        )
                                    ) : (
                                        <span className="badge badge--done">—</span>
                                    )}
                                </td>
                                <td className="admin-catalog__actions">
                                    {!readonly && (
                                        <button type="button" className="admin__edit" onClick={() => startEdit(item)}>
                                            Editar
                                        </button>
                                    )}
                                    {item.estado === undefined || item.estado !== 0 ? (
                                        <button type="button" className="admin__danger" onClick={() => toggle(item)}>
                                            {item.estado === undefined ? 'Eliminar' : 'Desactivar'}
                                        </button>
                                    ) : (
                                        <span className="badge badge--pending">Inactivo</span>
                                    )}
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>

                <p>
                    <Link href="/admin">← Dashboard</Link>
                </p>
            </div>
        </AdminLayout>
    );
}