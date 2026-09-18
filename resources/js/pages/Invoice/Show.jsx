import { Head, Link } from '@inertiajs/react';
import Layout from '../../components/Layout';

const money = (n) =>
    new Intl.NumberFormat('es-NI', { style: 'currency', currency: 'USD' }).format(Number(n || 0));

const dateShort = (d) => (d ? String(d).slice(0, 10) : '—');

export default function InvoiceShow({ shipment, corresponsal, cliente }) {
    return (
        <Layout title={`Factura #${shipment.id}`}>
            <Head title={`Factura #${shipment.id}`} />
            <div className="invoice">
                <div className="invoice__head">
                    <div>
                        <h1>Factura / Comprobante de envío</h1>
                        <p>
                            <strong>N.º de envío:</strong> #{shipment.id}
                        </p>
                        <p>
                            <strong>Código:</strong> {shipment.codigoenvio || '—'}
                        </p>
                        <p>
                            <strong>Fecha de solicitud:</strong> {dateShort(shipment.fechasol)}
                        </p>
                    </div>
                    <div className="invoice__brand">
                        <h2>Hermes</h2>
                        <p>Tienda en línea</p>
                    </div>
                </div>

                <div className="invoice__parties">
                    <div>
                        <h3>Cliente</h3>
                        {cliente ? (
                            <>
                                <p>{cliente.name}</p>
                                <p>{cliente.email}</p>
                                <p>{cliente.telefono}</p>
                                <p>{cliente.direccion}</p>
                            </>
                        ) : (
                            <p>—</p>
                        )}
                    </div>
                    {corresponsal && (
                        <div>
                            <h3>Destinatario / Corresponsal</h3>
                            <p>{corresponsal.nombre}</p>
                            <p>{corresponsal.Direccion}</p>
                            <p>{corresponsal.telefono}</p>
                            <p>{corresponsal.email}</p>
                            {corresponsal.banco && (
                                <p>
                                    {corresponsal.banco} · Cta. {corresponsal.numerocta}
                                </p>
                            )}
                        </div>
                    )}
                </div>

                <table className="cart-table invoice__items">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio unit.</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{shipment.producto}</td>
                            <td>{shipment.cantidad}</td>
                            <td>{money(shipment.precio)}</td>
                            <td>{money(shipment.subtotal)}</td>
                        </tr>
                    </tbody>
                </table>

                <div className="invoice__totals">
                    <p>
                        Subtotal: <strong>{money(shipment.subtotal)}</strong>
                    </p>
                    <p>
                        IVA (15 %): <strong>{money(shipment.iva)}</strong>
                    </p>
                    <p>
                        Envío: <strong>{money(shipment.precioenvio)}</strong>
                    </p>
                    <p className="invoice__total">
                        Total: <strong>{money(shipment.total)}</strong>
                    </p>
                    <p>
                        Empresa de envío: {shipment.empresa} · Pago: {shipment.metodoPago} · Destino: {shipment.ciudad || '—'}
                    </p>
                </div>

                <div className="invoice__actions">
                    <button type="button" className="btn btn--primary" onClick={() => window.print()}>
                        Imprimir factura
                    </button>
                    <Link href="/mis-envios" className="btn">
                        ← Mis envíos
                    </Link>
                </div>
            </div>
        </Layout>
    );
}