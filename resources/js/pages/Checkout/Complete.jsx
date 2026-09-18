import { Head, Link } from '@inertiajs/react';
import Layout from '../../components/Layout';
import { money } from '../../components/format';

export default function Complete({ order }) {
    return (
        <Layout>
            <Head title="Pedido recibido" />
            <div className="container page">
                <div className="order-complete">
                    <h1 className="page__title">¡Pedido recibido!</h1>
                    <p>
                        Gracias <strong>{order.customer.name}</strong>. Tu pedido{' '}
                        <strong>#{order.id}</strong> quedó guardado con estado pendiente.
                    </p>

                    <ul className="order-complete__items">
                        {order.items.map((item) => (
                            <li key={item.name}>
                                {item.qty} × {item.name} — {money(item.subtotal)}
                            </li>
                        ))}
                    </ul>

                    <p className="order-complete__total">
                        Total (IVA {order.totals.iva_rate}% incluido):{' '}
                        <strong>{money(order.totals.total)}</strong>
                    </p>

                    <Link href="/" className="btn btn--primary">
                        Seguir comprando
                    </Link>
                </div>
            </div>
        </Layout>
    );
}
