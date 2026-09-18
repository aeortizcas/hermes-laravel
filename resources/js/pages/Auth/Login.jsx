import { Head, Link, useForm } from '@inertiajs/react';
import Layout from '../../components/Layout';

export default function Login() {
    const form = useForm({ usuario: '', password: '' });

    function submit(e) {
        e.preventDefault();
        form.post('/login');
    }

    return (
        <Layout>
            <Head title="Iniciar sesión" />
            <div className="container page auth">
                <h1 className="page__title">Iniciar sesión</h1>
                <form className="form" onSubmit={submit}>
                    <label>
                        Usuario o correo *
                        <input
                            type="text"
                            value={form.data.usuario}
                            onChange={(e) => form.setData('usuario', e.target.value)}
                            autoComplete="username"
                        />
                        {form.errors.usuario && <p className="form-error">{form.errors.usuario}</p>}
                    </label>
                    <label>
                        Contraseña *
                        <input
                            type="password"
                            value={form.data.password}
                            onChange={(e) => form.setData('password', e.target.value)}
                            autoComplete="current-password"
                        />
                        {form.errors.password && <p className="form-error">{form.errors.password}</p>}
                    </label>
                    <button type="submit" className="btn btn--primary" disabled={form.processing}>
                        {form.processing ? 'Entrando…' : 'Entrar'}
                    </button>
                    <p className="auth__switch">
                        ¿No tienes cuenta? <Link href="/registro">Regístrate</Link>
                    </p>
                </form>
            </div>
        </Layout>
    );
}