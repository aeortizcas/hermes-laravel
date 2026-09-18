import { Head, Link, useForm } from '@inertiajs/react';
import Layout from '../../components/Layout';
import { money } from '../../components/format';

export default function ShipmentCheckout({ lines, totals, companies, methods }) {
    const initial = {};
    lines.forEach((line) => {
        initial[line.product.id] = { cia: '', pago: '' };
    });
    const form = useForm({ shipping: initial });

    function setOpt(productId, key, value) {
        form.setData('shipping', {
            ...form.data.shipping,
            [productId]: { ...form.data.shipping[productId], [key]: value },
        });
    }

    function submit(e) {
        e.preventDefault();
        form.post('/pedido');
    }

    return (
        <Layout>
            <Head title="Tu pedido" />
            <div className="container page">
                <h1 className="page__title">Tu pedido</h1>
                <p className="page__lead">
                    Elige la empresa de envío y el método de pago para cada producto, igual que en la
                    tienda anterior.
                </p>

                <form className="shipment-form" onSubmit={submit}>
                    {lines.map((line) => {
                        const opts = form.data.shipping[line.product.id] ?? { cia: '', pago: '' };
                        return (
                            <div className="shipment-card" key={line.product.id}>
                                <div className="shipment-card__head">
                                    <div>
                                        <strong>{line.qty} × {line.product.name}</strong>
                                        <p className="shipment-card__meta">
                                            {money(line.product.price)} c/u · subtotal {money(line.subtotal)}
                                        </p>
                                    </div>
                                    {form.errors[`shipping.${line.product.id}.cia`] && (
                                        <p className="form-error">
                                            {form.errors[`shipping.${line.product.id}.cia`]}
                                        </p>
                                    )}
                                </div>
                                <div className="shipment-card__opts">
                                    <div>
                                        <span className="shipment-card__label">Empresa de envío *</span>
                                        {companies.map((c) => (
                                            <label className="radio" key={c.id}>
                                                <input
                                                    type="radio"
                                                    name={`cia-${line.product.id}`}
                                                    checked={opts.cia === String(c.id)}
                                                    onChange={() => setOpt(line.product.id, 'cia', String(c.id))}
                                                />
                                                {c.name}
                                            </label>
                                        ))}
                                    </div>
                                    <div>
                                        <span className="shipment-card__label">Método de pago *</span>
                                        {methods.map((m) => (
                                            <label className="radio" key={m.id}>
                                                <input
                                                    type="radio"
                                                    name={`pago-${line.product.id}`}
                                                    checked={opts.pago === String(m.id)}
                                                    onChange={() => setOpt(line.product.id, 'pago', String(m.id))}
                                                />
                                                {m.name}
                                            </label>
                                        ))}
                                    </div>
                                </div>
                            </div>
                        );
                    })}

                    {form.errors.shipping && <p className="form-error">{form.errors.shipping}</p>}

                    <aside className="cart-summary">
                        <p>
                            Subtotal: <strong>{money(totals.subtotal)}</strong>
                        </p>
                        <p>
                            IVA ({totals.iva_rate}%): <strong>{money(totals.iva)}</strong>
                        </p>
                        <p className="cart-summary__total">
                            Total (sin envío): <strong>{money(totals.total)}</strong>
                        </p>
                        <button type="submit" className="btn btn--primary" disabled={form.processing}>
                            {form.processing ? 'Registrando…' : 'Registrar pedido'}
                        </button>
                        <p className="checkout__note">
                            El costo de envío se calcula al elegir el destino en el siguiente paso.
                        </p>
                        <p>
                            <Link href="/carrito">← Volver al carrito</Link>
                        </p>
                    </aside>
                </form>
            </div>
        </Layout>
    );
}