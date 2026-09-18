import { Head, Link, useForm } from '@inertiajs/react';
import Layout from '../../components/Layout';

export default function Register({ cities }) {
    const form = useForm({
        ciudad: '',
        usuario: '',
        email: '',
        primerNombre: '',
        segundoNombre: '',
        primerApellido: '',
        segundoApellido: '',
        telefono: '',
        direccion: '',
        password: '',
        password_confirmation: '',
    });

    function submit(e) {
        e.preventDefault();
        form.post('/registro');
    }

    return (
        <Layout>
            <Head title="Crear cuenta" />
            <div className="container page auth">
                <h1 className="page__title">Crear cuenta</h1>
                <form className="form" onSubmit={submit}>
                    <label>
                        Ciudad
                        <select value={form.data.ciudad} onChange={(e) => form.setData('ciudad', e.target.value)}>
                            <option value="">— Selecciona —</option>
                            {cities.map((c) => (
                                <option key={c.id} value={c.id}>
                                    {c.name}
                                </option>
                            ))}
                        </select>
                    </label>
                    <label>
                        Usuario *
                        <input
                            type="text"
                            value={form.data.usuario}
                            onChange={(e) => form.setData('usuario', e.target.value)}
                            autoComplete="username"
                        />
                        {form.errors.usuario && <p className="form-error">{form.errors.usuario}</p>}
                    </label>
                    <label>
                        Correo electrónico *
                        <input
                            type="email"
                            value={form.data.email}
                            onChange={(e) => form.setData('email', e.target.value)}
                            autoComplete="email"
                        />
                        {form.errors.email && <p className="form-error">{form.errors.email}</p>}
                    </label>
                    <div className="form__row">
                        <label>
                            Primer nombre *
                            <input
                                type="text"
                                value={form.data.primerNombre}
                                onChange={(e) => form.setData('primerNombre', e.target.value)}
                            />
                            {form.errors.primerNombre && (
                                <p className="form-error">{form.errors.primerNombre}</p>
                            )}
                        </label>
                        <label>
                            Segundo nombre
                            <input
                                type="text"
                                value={form.data.segundoNombre}
                                onChange={(e) => form.setData('segundoNombre', e.target.value)}
                            />
                        </label>
                    </div>
                    <div className="form__row">
                        <label>
                            Primer apellido *
                            <input
                                type="text"
                                value={form.data.primerApellido}
                                onChange={(e) => form.setData('primerApellido', e.target.value)}
                            />
                            {form.errors.primerApellido && (
                                <p className="form-error">{form.errors.primerApellido}</p>
                            )}
                        </label>
                        <label>
                            Segundo apellido
                            <input
                                type="text"
                                value={form.data.segundoApellido}
                                onChange={(e) => form.setData('segundoApellido', e.target.value)}
                            />
                        </label>
                    </div>
                    <div className="form__row">
                        <label>
                            Teléfono *
                            <input
                                type="tel"
                                value={form.data.telefono}
                                onChange={(e) => form.setData('telefono', e.target.value)}
                            />
                            {form.errors.telefono && <p className="form-error">{form.errors.telefono}</p>}
                        </label>
                        <label>
                            Dirección *
                            <input
                                type="text"
                                value={form.data.direccion}
                                onChange={(e) => form.setData('direccion', e.target.value)}
                            />
                            {form.errors.direccion && <p className="form-error">{form.errors.direccion}</p>}
                        </label>
                    </div>
                    <div className="form__row">
                        <label>
                            Contraseña *
                            <input
                                type="password"
                                value={form.data.password}
                                onChange={(e) => form.setData('password', e.target.value)}
                                autoComplete="new-password"
                            />
                            {form.errors.password && <p className="form-error">{form.errors.password}</p>}
                        </label>
                        <label>
                            Repite la contraseña *
                            <input
                                type="password"
                                value={form.data.password_confirmation}
                                onChange={(e) => form.setData('password_confirmation', e.target.value)}
                                autoComplete="new-password"
                            />
                        </label>
                    </div>
                    <button type="submit" className="btn btn--primary" disabled={form.processing}>
                        {form.processing ? 'Creando…' : 'Registrarme'}
                    </button>
                    <p className="auth__switch">
                        ¿Ya tienes cuenta? <Link href="/login">Inicia sesión</Link>
                    </p>
                </form>
            </div>
        </Layout>
    );
}