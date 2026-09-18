import { Head, useForm } from '@inertiajs/react';

export default function AdminLogin() {
    const form = useForm({ usuario: '', password: '' });

    function submit(e) {
        e.preventDefault();
        form.post('/admin/login');
    }

    return (
        <div className="admin-login">
            <Head title="Acceso admin" />
            <form className="form admin-login__card" onSubmit={submit}>
                <h1>Acceso de administración</h1>
                <label>
                    Usuario
                    <input
                        type="text"
                        value={form.data.usuario}
                        onChange={(e) => form.setData('usuario', e.target.value)}
                        autoComplete="username"
                    />
                    {form.errors.usuario && <p className="form-error">{form.errors.usuario}</p>}
                </label>
                <label>
                    Contraseña
                    <input
                        type="password"
                        value={form.data.password}
                        onChange={(e) => form.setData('password', e.target.value)}
                        autoComplete="current-password"
                    />
                </label>
                <button type="submit" className="btn btn--primary" disabled={form.processing}>
                    {form.processing ? 'Entrando…' : 'Entrar'}
                </button>
            </form>
        </div>
    );
}