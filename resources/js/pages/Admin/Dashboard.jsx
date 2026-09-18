import { Link } from '@inertiajs/react';
import AdminLayout from './AdminLayout';

export default function Dashboard({ counts }) {
    const cards = [
        { label: 'Productos', value: counts.products, href: '/admin/productos' },
        { label: 'Categorías', value: counts.categories },
        { label: 'Clientes', value: counts.customers },
        { label: 'Pedidos', value: counts.orders },
        { label: 'Envíos', value: counts.shipments },
        { label: 'Envíos pendientes', value: counts.pendingShipments },
    ];

    return (
        <AdminLayout title="Dashboard">
            <div className="admin__content">
                <h1 className="page__title">Dashboard</h1>
                <div className="admin-cards">
                    {cards.map((c) => (
                        <div className="admin-card" key={c.label}>
                            <span className="admin-card__value">{c.value}</span>
                            <span className="admin-card__label">{c.label}</span>
                            {c.href && (
                                <Link href={c.href} className="admin-card__link">
                                    Ver →
                                </Link>
                            )}
                        </div>
                    ))}
                </div>
            </div>
        </AdminLayout>
    );
}