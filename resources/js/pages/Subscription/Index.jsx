import { Head, Link, router, useForm } from '@inertiajs/react';
import Layout from '../../components/Layout';

const PLANS = [
    {
        value: 'personal',
        name: 'Membresía Personal',
        features: ['Acceso a funciones básicas', 'Ideal para usuarios individuales', 'Precio accesible'],
    },
    {
        value: 'business',
        name: 'Membresía Empresarial',
        features: ['Herramientas avanzadas y análisis', 'Perfecto para pequeñas empresas', 'Soporte prioritario'],
    },
];

export default function SubscriptionIndex({ current }) {
    const form = useForm({ membership: current?.membership_type ?? '' });

    function submit(e) {
        e.preventDefault();
        form.post('/suscripcion', { preserveScroll: true });
    }

    return (
        <Layout title="Membresías">
            <Head title="Membresías" />
            <div className="store-container page">
                <h1 className="page__title">Elige tu membresía</h1>
                {current ? (
                    <p className="store-flash">
                        Tu membresía actual: <strong>{current.membership_type === 'personal' ? 'Personal' : 'Empresarial'}</strong>{' '}
                        {current.end_date && `(vence el ${current.end_date.slice(0, 10)})`}
                    </p>
                ) : (
                    <p>No tienes una membresía activa. Selecciona un plan para continuar.</p>
                )}

                <form onSubmit={submit}>
                    <div className="membership-options">
                        {PLANS.map((plan) => (
                            <label key={plan.value} className={`membership-card ${form.data.membership === plan.value ? 'membership-card--active' : ''}`}>
                                <input
                                    type="radio"
                                    name="membership"
                                    value={plan.value}
                                    checked={form.data.membership === plan.value}
                                    onChange={(e) => form.setData('membership', e.target.value)}
                                />
                                <h3>{plan.name}</h3>
                                <ul>
                                    {plan.features.map((f) => (
                                        <li key={f}>✔ {f}</li>
                                    ))}
                                </ul>
                            </label>
                        ))}
                    </div>
                    {form.errors.membership && <p className="form-error">{form.errors.membership}</p>}
                    <button type="submit" className="btn btn--primary" disabled={form.processing}>
                        {form.processing ? 'Guardando…' : 'Suscribirse'}
                    </button>
                </form>

                <p>
                    <Link href="/">← Volver a la tienda</Link>
                </p>
            </div>
        </Layout>
    );
}