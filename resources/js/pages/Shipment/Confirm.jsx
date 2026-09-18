import { Head, Link, useForm } from '@inertiajs/react';
import Layout from '../../components/Layout';
import { money } from '../../components/format';

export default function Confirm({ shipment, banks }) {
    const form = useForm({
        name: '',
        direccion: '',
        telefono: '',
        email: '',
        banco: '',
        numero: '',
    });

    function submit(e) {
        e.preventDefault();
        form.post(`/mis-envios/${shipment.id}/confirmar`);
    }

    return (
        <Layout>
            <Head title="Confirmar envío" />
            <div className="container page">
                <h1 className="page__title">Confirmar envío #{shipment.id}</h1>

                <div className="checkout__grid">
                    <div className="shipment-card">
                        <div className="shipment-card__head">
                            <div>
                                <strong>{shipment.producto}</strong>
                                <p className="shipment-card__meta">
                                    {shipment.cantidad} × {money(shipment.precio)} · {shipment.empresa} ·{' '}
                                    {shipment.metodoPago}
                                </p>
                            </div>
                        </div>
                        <dl className="shipment-totals">
                            <div>
                                <dt>Subtotal</dt>
                                <dd>{money(shipment.subtotal)}</dd>
                            </div>
                            <div>
                                <dt>IVA (15%)</dt>
                                <dd>{money(shipment.iva)}</dd>
                            </div>
                            <div>
                                <dt>Envío a {shipment.ciudad}</dt>
                                <dd>{money(shipment.precioenvio)}</dd>
                            </div>
                            <div className="shipment-totals__total">
                                <dt>Total</dt>
                                <dd>{money(shipment.total)}</dd>
                            </div>
                        </dl>
                    </div>

                    <form className="form" onSubmit={submit}>
                        <h2>Datos del corresponsal</h2>
                        <label>
                            Nombre completo *
                            <input
                                type="text"
                                value={form.data.name}
                                onChange={(e) => form.setData('name', e.target.value)}
                            />
                            {form.errors.name && <p className="form-error">{form.errors.name}</p>}
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
                        <div className="form__row">
                            <label>
                                Teléfono *
                                <input
                                    type="tel"
                                    value={form.data.telefono}
                                    onChange={(e) => form.setData('telefono', e.target.value)}
                                />
                                {form.errors.telefono && (
                                    <p className="form-error">{form.errors.telefono}</p>
                                )}
                            </label>
                            <label>
                                Correo *
                                <input
                                    type="email"
                                    value={form.data.email}
                                    onChange={(e) => form.setData('email', e.target.value)}
                                />
                                {form.errors.email && <p className="form-error">{form.errors.email}</p>}
                            </label>
                        </div>
                        {shipment.metodoPago?.toLowerCase().includes('transferencia') && (
                            <>
                                <label>
                                    Banco
                                    <select
                                        value={form.data.banco}
                                        onChange={(e) => form.setData('banco', e.target.value)}
                                    >
                                        <option value="">— Selecciona —</option>
                                        {banks.map((b) => (
                                            <option key={b.id} value={b.id}>
                                                {b.name}
                                            </option>
                                        ))}
                                    </select>
                                </label>
                                <label>
                                    Número de cuenta
                                    <input
                                        type="text"
                                        value={form.data.numero}
                                        onChange={(e) => form.setData('numero', e.target.value)}
                                    />
                                </label>
                            </>
                        )}
                        <button type="submit" className="btn btn--primary" disabled={form.processing}>
                            {form.processing ? 'Confirmando…' : 'Confirmar envío'}
                        </button>
                        <p>
                            <Link href="/mis-envios">← Mis envíos</Link>
                        </p>
                    </form>
                </div>
            </div>
        </Layout>
    );
}