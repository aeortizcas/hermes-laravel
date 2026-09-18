import { Head, Link, router, useForm } from '@inertiajs/react';
import Layout from '../../components/Layout';
import { money } from '../../components/format';

function ShipmentRow({ shipment, cities }) {
    const form = useForm({ city: shipment.ciudadId ?? '' });

    function pickCity(e) {
        e.preventDefault();
        form.post(`/mis-envios/${shipment.id}/destino`, { preserveScroll: true });
    }

    return (
        <tr>
            <td>
                <strong>{shipment.producto}</strong>
                <p className="shipment-table__meta">N° {shipment.id} · {shipment.cantidad} unidad(es)</p>
            </td>
            <td>{shipment.empresa}</td>
            <td>{shipment.metodoPago}</td>
            <td>{money(shipment.precio)}</td>
            <td>{money(shipment.total)}</td>
            <td>
                {shipment.pendienteDestino ? (
                    <form className="shipment-destino" onSubmit={pickCity}>
                        <select
                            value={form.data.city}
                            onChange={(e) => form.setData('city', e.target.value)}
                            aria-label={`Destino de ${shipment.producto}`}
                        >
                            <option value="">— Ciudad —</option>
                            {cities.map((c) => (
                                <option key={c.id} value={c.id}>
                                    {c.name}
                                </option>
                            ))}
                        </select>
                        <button type="submit" className="btn btn--small" disabled={form.processing}>
                            Elegir
                        </button>
                    </form>
                ) : (
                    <>
                        <span className="shipment-table__dest">{shipment.ciudad}</span>
                        {shipment.corresponsal?.direccion && (
                            <p className="shipment-table__meta">
                                📍 {shipment.corresponsal.direccion}
                            </p>
                        )}
                        <p className="shipment-table__meta">Envío: {money(shipment.precioenvio)}</p>
                    </>
                )}
            </td>
            <td>
                {shipment.pendienteDestino ? (
                    <span className="badge badge--pending">Paso 1: destino</span>
                ) : shipment.codigoenvio ? (
                    <span className="badge badge--done">Confirmado</span>
                ) : (
                    <Link href={`/mis-envios/${shipment.id}/confirmar`} className="btn btn--small">
                        Confirmar
                    </Link>
                )}
            </td>
            <td>
                <Link href={`/mis-envios/${shipment.id}`}>Ver</Link>
            </td>
        </tr>
    );
}

export default function Shipments({ shipments, cities }) {
    return (
        <Layout>
            <Head title="Mis envíos" />
            <div className="container page">
                <h1 className="page__title">Mis envíos</h1>
                <p className="page__lead">
                    Aquí aparecen tus pedidos con envío (tabla tbl_rastreo del sistema anterior).
                </p>

                {!shipments.length ? (
                    <p className="store-empty">Todavía no tienes envíos registrados.</p>
                ) : (
                    <table className="cart-table shipment-table">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Empresa</th>
                                <th>Pago</th>
                                <th>Precio</th>
                                <th>Total</th>
                                <th>Destino</th>
                                <th>Estado</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            {shipments.map((s) => (
                                <ShipmentRow key={s.id} shipment={s} cities={cities} />
                            ))}
                        </tbody>
                    </table>
                )}
            </div>
        </Layout>
    );
}