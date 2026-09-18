import { Head, Link, useForm } from '@inertiajs/react';
import Layout from '../../components/Layout';
import { money } from '../../components/format';

export default function Checkout({ lines, totals }) {
    const form = useForm({ name: '', email: '', telefono: '', direccion: '' });

    function submit(e) {
        e.preventDefault();
        form.post('/checkout');
    }

    return (
        <Layout>
            <Head title="Finalizar compra" />
            <div className="container page checkout">
                <h1 className="page__title">Finalizar compra</h1>

                <div className="checkout__grid">
                    <form className="form" onSubmit={submit}>
                        <h2>Tus datos</h2>
                        <label>
                            Nombre *
                            <input
                                type="text"
                                value={form.data.name}
                                onChange={(e) => form.setData('name', e.target.value)}
                            />
                            {form.errors.name && (
                                <p className="form-error">{form.errors.name}</p>
                            )}
                        </label>
                        <label>
                            Correo electrónico *
                            <input
                                type="email"
                                value={form.data.email}
                                onChange={(e) => form.setData('email', e.target.value)}
                            />
                            {form.errors.email && (
                                <p className="form-error">{form.errors.email}</p>
                            )}
                        </label>
                        <label>
                            Teléfono
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
                            Dirección
                            <input
                                type="text"
                                value={form.data.direccion}
                                onChange={(e) => form.setData('direccion', e.target.value)}
                            />
                            {form.errors.direccion && (
                                <p className="form-error">{form.errors.direccion}</p>
                            )}
                        </label>

                        <button type="submit" className="btn btn--primary" disabled={form.processing}>
                            {form.processing ? 'Procesando…' : 'Confirmar pedido'}
                        </button>
                        <p className="checkout__note">
                            El pago (PayPal / transferencia) se habilitará en la fase 2 de la
                            migración; por ahora el pedido queda guardado como pendiente.
                        </p>
                    </form>

                    <aside className="checkout__summary">
                        <h2>Resumen</h2>
                        {lines.map((line) => (
                            <div className="checkout__line" key={line.product.id}>
                                <span>
                                    {line.qty} × {line.product.name}
                                </span>
                                <strong>{money(line.subtotal)}</strong>
                            </div>
                        ))}
                        <hr />
                        <p>
                            Subtotal <strong>{money(totals.subtotal)}</strong>
                        </p>
                        <p>
                            IVA ({totals.iva_rate}%) <strong>{money(totals.iva)}</strong>
                        </p>
                        <p className="checkout__total">
                            Total <strong>{money(totals.total)}</strong>
                        </p>
                    </aside>
                </div>
            </div>
        </Layout>
    );
}
