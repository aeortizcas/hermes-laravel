import { Head, Link, router, useForm } from '@inertiajs/react';
import AdminLayout from '../AdminLayout';
import { money } from '../../../components/format';

const STAGE_LABEL = {
    pending: 'Pendiente',
    processing: 'Procesado',
    shipped: 'Entregado al courier',
    delivered: 'Entregado',
};

function StageBadge({ stage }) {
    const cls =
        stage === 'delivered' ? 'badge--done' : stage === 'pending' ? 'badge--pending' : 'badge--processing';
    return <span className={`badge ${cls}`}>{STAGE_LABEL[stage] ?? stage}</span>;
}

function ShipmentRow({ shipment }) {
    const form = useForm({ action: '', codigoenviocia: '' });

    function advance(action) {
        const payload = { action };
        if (action === 'encargar') {
            const code = prompt('Código de seguimiento del courier (codigoenviocia):', form.data.codigoenviocia);
            if (code === null) return;
            payload.codigoenviocia = code;
        }
        form.transform(() => payload).post(`/admin/envios/${shipment.id}/avanzar`, {
            preserveScroll: true,
        });
    }

    return (
        <tr>
            <td>
                <Link href={`/admin/envios/${shipment.id}`}>#{shipment.id}</Link>
            </td>
            <td>
                {shipment.cliente || shipment.usuario}
                <p className="shipment-table__meta">{shipment.usuario}</p>
            </td>
            <td>
                {shipment.producto}
                <p className="shipment-table__meta">
                    {shipment.cantidad} u · {shipment.ciudad}
                </p>
            </td>
            <td>{shipment.empresa}</td>
            <td>{shipment.metodoPago}</td>
            <td>{money(shipment.total)}</td>
            <td className="shipment-dates">
                <span title="Solicitud">{shipment.fechasol ?? '—'}</span>
                <span title="Procesado">{shipment.fechapro ?? '—'}</span>
                <span title="Encargo">{shipment.fechaencargo ?? '—'}</span>
                <span title="Entrega">{shipment.fechaentrega ?? '—'}</span>
            </td>
            <td>
                <StageBadge stage={shipment.etapa} />
                {shipment.codigoenviocia && (
                    <p className="shipment-table__meta">Courier: {shipment.codigoenviocia}</p>
                )}
            </td>
            <td>
                {shipment.etapa === 'pending' && (
                    <button type="button" className="btn btn--small" onClick={() => advance('procesar')}>
                        Procesar
                    </button>
                )}
                {shipment.etapa === 'processing' && (
                    <button type="button" className="btn btn--small" onClick={() => advance('encargar')}>
                        Entregar a courier
                    </button>
                )}
                {shipment.etapa === 'shipped' && (
                    <button type="button" className="btn btn--small" onClick={() => advance('entregar')}>
                        Marcar entregado
                    </button>
                )}
                {shipment.etapa === 'delivered' && <span className="shipment-table__meta">✓</span>}
            </td>
        </tr>
    );
}

export default function AdminShipments({ shipments, counts, status }) {
    const filters = [
        ['all', 'Todos'],
        ['pending', `Pendientes (${counts.pending})`],
        ['processing', `Procesados (${counts.processing})`],
        ['delivered', `Entregados (${counts.delivered})`],
    ];

    return (
        <AdminLayout title="Envíos">
            <Head title="Envíos" />
            <div className="admin__content">
                <h1 className="page__title">Envíos (tbl_rastreo)</h1>

                <nav className="category-nav admin-tabs">
                    {filters.map(([value, label]) => (
                        <Link key={value} href={`/admin/envios?status=${value}`} className={status === value ? 'is-active' : ''}>
                            {label}
                        </Link>
                    ))}
                </nav>

                <table className="cart-table admin-shipments">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Producto</th>
                            <th>Empresa</th>
                            <th>Pago</th>
                            <th>Total</th>
                            <th>Fechas (sol/pro/enc/ent)</th>
                            <th>Etapa</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        {shipments.map((s) => (
                            <ShipmentRow key={s.id} shipment={s} />
                        ))}
                        {!shipments.length && (
                            <tr>
                                <td colSpan={9} className="store-empty">
                                    No hay envíos en esta vista.
                                </td>
                            </tr>
                        )}
                    </tbody>
                </table>
            </div>
        </AdminLayout>
    );
}