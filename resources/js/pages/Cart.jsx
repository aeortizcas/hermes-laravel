import { Head, Link, router, useForm } from '@inertiajs/react';
import Layout from '../components/Layout';
import Price from '../components/Price';

function Line({ line }) {
    const { product, qty, subtotal } = line;
    const form = useForm({ qty });
    const remove = useForm({});

    function updateQty(e) {
        e.preventDefault();
        form.post(`/carrito/${product.id}`, { preserveScroll: true });
    }

    return (
        <tr>
            <td>
                <Link href={`/producto/${product.id}`} className="cart-line__thumb">
                    {product.cover ? (
                        <img src={product.cover} alt={product.name} loading="lazy" />
                    ) : (
                        <span className="product-card__placeholder">Sin imagen</span>
                    )}
                </Link>
            </td>
            <td>
                <Link href={`/producto/${product.id}`} className="cart-line__name">
                    {product.name}
                </Link>
            </td>
            <td className="cart-line__price">
                <Price value={product.price} />
            </td>
            <td>
                <form className="cart-line__qty" onSubmit={updateQty}>
                    <input
                        type="number"
                        min="0"
                        max="99"
                        value={form.data.qty}
                        onChange={(e) => form.setData('qty', e.target.value)}
                        aria-label={`Cantidad de ${product.name}`}
                    />
                    <button type="submit" disabled={form.processing}>
                        Actualizar
                    </button>
                </form>
            </td>
            <td>
                <Price value={subtotal} />
            </td>
            <td>
                <button
                    type="button"
                    className="cart-line__remove"
                    onClick={() => router.delete(`/carrito/${product.id}`, { preserveScroll: true })}
                    disabled={remove.processing}
                >
                    Quitar
                </button>
            </td>
        </tr>
    );
}

export default function Cart({ lines, totals }) {
    if (!lines.length) {
        return (
            <Layout>
                <Head title="Carrito" />
                <div className="container page">
                    <h1 className="page__title">Tu carrito está vacío</h1>
                    <p>
                        <Link href="/">Ver productos</Link>
                    </p>
                </div>
            </Layout>
        );
    }

    return (
        <Layout>
            <Head title="Carrito" />
            <div className="container page">
                <h1 className="page__title">Carrito</h1>

                <div className="cart-grid">
                    <table className="cart-table">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Producto</th>
                                <th>Precio</th>
                                <th>Cantidad</th>
                                <th>Subtotal</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            {lines.map((line) => (
                                <Line key={line.product.id} line={line} />
                            ))}
                        </tbody>
                    </table>

                    <aside className="cart-summary">
                        <h2 className="cart-summary__title">Resumen del pedido</h2>
                        <p>
                            Subtotal: <strong>{totals.subtotal.toFixed(2)}</strong>
                        </p>
                        <p>
                            IVA ({totals.iva_rate}%): <strong>{totals.iva.toFixed(2)}</strong>
                        </p>
                        <p className="cart-summary__total">
                            Total: <strong>${totals.total.toFixed(2)}</strong>
                        </p>
                        <Link href="/pedido" className="btn btn--primary">
                            Tramitar con envío
                        </Link>
                        <Link href="/checkout" className="btn btn--outline">
                            Pedido simple (sin envío)
                        </Link>
                        <p className="cart-summary__note">
                            El envío se calcula al elegir la ciudad de destino.
                        </p>
                    </aside>
                </div>
            </div>
        </Layout>
    );
}