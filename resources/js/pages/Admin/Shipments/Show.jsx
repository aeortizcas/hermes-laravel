import { Head, Link, useForm } from '@inertiajs/react';
import AdminLayout from '../AdminLayout';
import { money } from '../../../components/format';

const STEPS = [
    ['solicitud', 'Solicitud', 'fechasol'],
    ['procesado', 'Procesado', 'fechapro'],
    ['encargo', 'Entregado al courier', 'fechaencargo'],
    ['entrega', 'Entregado', 'fechaentrega'],
];

export default function ShipmentShow({ shipment }) {
    const form = useForm({ action: '', codigoenviocia: shipment.codigoenviocia ?? '' });

    function advance(e, action) {
        e.preventDefault();
        form.transform(() => ({
            action,
            codigoenviocia: form.data.codigoenviocia,
        })).post(`/admin/envios/${shipment.id}/avanzar`);
    }

    return (
        <AdminLayout title={`Envío #${shipment.id}`}>
            <Head title={`Envío #${shipment.id}`} />
            <div className="admin__content">
                <div className="admin__head">
                    <h1 className="page__title">Envío #{shipment.id}</h1>
                    <div className="admin__head-actions">
                        <Link href={`/admin/envios/${shipment.id}/factura`} className="btn">
                            Factura
                        </Link>
                        <Link href="/admin/envios" className="btn">
                            ← Todos los envíos
                        </Link>
                    </div>
                </div>

                <div className="shipment-card">
                    <dl className="shipment-totals">
                        <div>
                            <dt>Cliente</dt>
                            <dd>
                                {shipment.cliente || '—'} ({shipment.usuario})
                            </dd>
                        </div>
                        <div>
                            <dt>Producto</dt>
                            <dd>
                                {shipment.producto} × {shipment.cantidad}
                            </dd>
                        </div>
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
                            <dd>{shipment.ciudad}</dd>
                        </div>
                        <div>
                            <dt>Subtotal / IVA / Envío</dt>
                            <dd>
                                {money(shipment.subtotal)} / {money(shipment.iva)} / {money(shipment.precioenvio)}
                            </dd>
                        </div>
                        <div className="shipment-totals__total">
                            <dt>Total</dt>
                            <dd>{money(shipment.total)}</dd>
                        </div>
                        <div>
                            <dt>Código interno</dt>
                            <dd>{shipment.codigoenvio ?? '—'}</dd>
                        </div>
                        <div>
                            <dt>Código del courier</dt>
                            <dd>
                                {shipment.codigoenviocia ?? (
                                    <em className="shipment-card__meta">aún no asignado</em>
                                )}
                            </dd>
                        </div>
                    </dl>
                </div>

                <h2 className="admin-subtitle">Etapas</h2>
                <div className="shipment-timeline">
                    {STEPS.map(([key, label, field], i) => {
                        const done = shipment[field] !== null;
                        const isNext = !done && STEPS.slice(0, i).every(([, , f]) => shipment[f] !== null);
                        return (
                            <div key={key} className={`shipment-timeline__step ${done ? 'is-done' : ''}`}>
                                <span className="shipment-timeline__dot">{done ? '✓' : i + 1}</span>
                                <div>
                                    <strong>{label}</strong>
                                    <p className="shipment-card__meta">
                                        {shipment[field] ?? 'Pendiente'}
                                    </p>
                                </div>
                            </div>
                        );
                    })}
                </div>

                <div className="admin-form shipment-card" style={{ maxWidth: 420 }}>
                    <h2>Avanzar etapa</h2>
                    {shipment.etapa === 'pending' && (
                        <form className="admin-actions" onSubmit={(e) => advance(e, 'procesar')}>
                            <button type="submit" className="btn btn--primary">
                                Marcar como procesado (fechapro)
                            </button>
                        </form>
                    )}
                    {shipment.etapa === 'processing' && (
                        <form className="admin-actions" onSubmit={(e) => advance(e, 'encargar')}>
                            <label>
                                Código de seguimiento del courier (codigoenviocia)
                                <input
                                    type="text"
                                    value={form.data.codigoenviocia}
                                    onChange={(e) => form.setData('codigoenviocia', e.target.value)}
                                />
                            </label>
                            <button type="submit" className="btn btn--primary">
                                Entregar a courier (fechaencargo)
                            </button>
                        </form>
                    )}
                    {shipment.etapa === 'shipped' && (
                        <form className="admin-actions" onSubmit={(e) => advance(e, 'entregar')}>
                            <button type="submit" className="btn btn--primary">
                                Marcar entregado (fechaentrega)
                            </button>
                        </form>
                    )}
                    {shipment.etapa === 'delivered' && (
                        <p className="shipment-card__meta">Este envío ya fue entregado.</p>
                    )}
                </div>
            </div>
        </AdminLayout>
    );
}