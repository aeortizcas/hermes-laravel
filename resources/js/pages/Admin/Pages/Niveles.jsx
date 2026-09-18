import { Head, Link, router, useForm } from '@inertiajs/react';
import AdminLayout from '../AdminLayout';

export default function Niveles({ permissions, users, pages }) {
    const form = useForm({ user_id: '', page_id: '', access_type: 'allow' });

    function submit(e) {
        e.preventDefault();
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

    return (
        <AdminLayout title="Niveles (permisos)">
            <Head title="Niveles" />
            <div className="admin__content">
                <h1 className="page__title">Niveles — permisos de usuario por página</h1>

                <form className="form admin-form admin-form--row admin-form--wrap" onSubmit={submit}>
                    <label className="admin-form__field">
                        Usuario
                        <select value={form.data.user_id} onChange={(e) => form.setData('user_id', e.target.value)}>
                            <option value="">— Seleccionar —</option>
                            {users.map((u) => (
                                <option key={u.value} value={u.value}>
                                    {u.label}
                                </option>
                            ))}
                        </select>
                        {form.errors.user_id && <p className="form-error">{form.errors.user_id}</p>}
                    </label>
                    <label className="admin-form__field">
                        Página
                        <select value={form.data.page_id} onChange={(e) => form.setData('page_id', e.target.value)}>
                            <option value="">— Seleccionar —</option>
                            {pages.map((p) => (
                                <option key={p.value} value={p.value}>
                                    {p.label}
                                </option>
                            ))}
                        </select>
                        {form.errors.page_id && <p className="form-error">{form.errors.page_id}</p>}
                    </label>
                    <label className="admin-form__field">
                        Acceso
                        <select value={form.data.access_type} onChange={(e) => form.setData('access_type', e.target.value)}>
                            <option value="allow">Permitir</option>
                            <option value="deny">Denegar</option>
                        </select>
                    </label>
                    <div className="admin-form__actions">
                        <button type="submit" className="btn btn--primary" disabled={form.processing}>
                            {form.processing ? 'Guardando…' : 'Asignar permiso'}
                        </button>
                    </div>
                </form>

                <table className="cart-table admin-catalog">
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
                        {permissions.map((perm) => (
                            <tr key={perm.id}>
                                <td>{perm.id}</td>
                                <td>{perm.user_name}</td>
                                <td>{perm.page_name}</td>
                                <td>
                                    {perm.access_type === 'allow' ? (
                                        <span className="badge badge--done">Permitir</span>
                                    ) : (
                                        <span className="badge badge--pending">Denegar</span>
                                    )}
                                </td>
                                <td>
                                    <button type="button" className="admin__danger" onClick={() => remove(perm)}>
                                        Quitar
                                    </button>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>

                <p>
                    <Link href="/admin/paginas">← Páginas</Link> · <Link href="/admin">Dashboard</Link>
                </p>
            </div>
        </AdminLayout>
    );
}