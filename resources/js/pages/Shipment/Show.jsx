import { Head, Link } from '@inertiajs/react';
import Layout from '../../components/Layout';
import { money } from '../../components/format';

const STEPS = [
    ['solicitud', 'Solicitud', 'fechasol'],
    ['confirmado', 'Confirmado', 'codigoenvio'],
    ['procesado', 'Procesado', 'fechapro'],
    ['encargo', 'Entregado al courier', 'fechaencargo'],
    ['entrega', 'Entregado', 'fechaentrega'],
];

export default function Show({ shipment, corresponsal }) {
    return (
        <Layout>
            <Head title={`Envío #${shipment.id}`} />
            <div className="container page">
                <h1 className="page__title">Envío #{shipment.id}</h1>

                {shipment.codigoenvio && (
                    <div className="store-flash">
                        Código de seguimiento: <strong>{shipment.codigoenvio}</strong>
                    </div>
                )}

                <div className="shipment-card">
                    <h2 className="shipment-card__title">Estado del envío</h2>
                    <div className="shipment-timeline">
                        {STEPS.map(([key, label, field], i) => {
                            const value = shipment[field];
                            const done = !!value;
                            return (
                                <div key={key} className={`shipment-timeline__step ${done ? 'is-done' : ''}`}>
                                    <span className="shipment-timeline__dot">{done ? '✓' : i + 1}</span>
                                    <div>
                                        <strong>{label}</strong>
                                        <p className="shipment-card__meta">
                                            {field === 'codigoenvio' ? (done ? value : 'Pendiente') : (value ?? 'Pendiente')}
                                        </p>
                                    </div>
                                </div>
                            );
                        })}
                    </div>
                </div>

                <div className="shipment-card">
                    <div className="shipment-card__head">
                        <div>
                            <strong>{shipment.producto}</strong>
                            <p className="shipment-card__meta">
                                {shipment.cantidad} × {money(shipment.precio)}
                            </p>
                        </div>
                    </div>
                    <dl className="shipment-totals">
                        <div>
                            <dt>Empresa de envío</dt>
                            <dd>{shipment.empresa}</dd>
                        </div>
                        <div>
                            <dt>Método de pago</dt>
                            <dd>{shipment.metodoPago}</dd>
                        </div>
                        <div>
                            <dt>Destino</dt>
                            <dd>{shipment.ciudad ?? 'Pendiente'}</dd>
                        </div>
                        <div>
                            <dt>Fecha de solicitud</dt>
                            <dd>{shipment.fechasol}</dd>
                        </div>
                        <div>
                            <dt>Subtotal</dt>
                            <dd>{money(shipment.subtotal)}</dd>
                        </div>
                        <div>
                            <dt>IVA (15%)</dt>
                            <dd>{money(shipment.iva)}</dd>
                        </div>
                        <div>
                            <dt>Envío</dt>
                            <dd>{money(shipment.precioenvio)}</dd>
                        </div>
                        <div className="shipment-totals__total">
                            <dt>Total</dt>
                            <dd>{money(shipment.total)}</dd>
                        </div>
                    </dl>
                </div>

                <p>
                    <Link href={`/mis-envios/${shipment.id}/factura`} className="btn btn--primary">
                        Ver factura imprimible
                    </Link>
                </p>

                {corresponsal && (
                    <div className="shipment-card">
                        <h2>Corresponsal</h2>
                        <dl className="shipment-totals">
                            <div>
                                <dt>Nombre</dt>
                                <dd>{corresponsal.nombre}</dd>
                            </div>
                            <div>
                                <dt>Dirección</dt>
                                <dd>{corresponsal.Direccion}</dd>
                            </div>
                            <div>
                                <dt>Teléfono</dt>
                                <dd>{corresponsal.telefono}</dd>
                            </div>
                            <div>
                                <dt>Correo</dt>
                                <dd>{corresponsal.email}</dd>
                            </div>
                        </dl>
                    </div>
                )}

                <p>
                    <Link href="/mis-envios">← Mis envíos</Link>
                </p>
            </div>
        </Layout>
    );
}