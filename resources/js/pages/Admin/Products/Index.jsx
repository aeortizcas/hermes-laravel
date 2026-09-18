import { Head, Link, router } from '@inertiajs/react';
import AdminLayout from '../AdminLayout';
import { money } from '../../../components/format';

export default function ProductsIndex({ products }) {
    function remove(product) {
        if (confirm(`¿Desactivar el producto “${product.name}”?`)) {
            router.delete(`/admin/productos/${product.id}`);
        }
    }

    return (
        <AdminLayout title="Productos">
            <Head title="Productos" />
            <div className="admin__content">
                <div className="admin__head">
                    <h1 className="page__title">Productos</h1>
                    <Link href="/admin/productos/nuevo" className="btn btn--primary">
                        + Nuevo producto
                    </Link>
                </div>

                <table className="cart-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Categoría</th>
                            <th>Precio</th>
                            <th>Stock</th>
                            <th>Estado</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        {products.map((p) => (
                            <tr key={p.id}>
                                <td>{p.id}</td>
                                <td>{p.name}</td>
                                <td>{p.category ?? '—'}</td>
                                <td>{p.price !== null ? money(p.price) : '—'}</td>
                                <td>{p.stock}</td>
                                <td>
                                    {p.estado !== 0 ? (
                                        <span className="badge badge--done">Activo</span>
                                    ) : (
                                        <span className="badge badge--pending">Inactivo</span>
                                    )}
                                </td>
                                <td>
                                    <Link href={`/admin/productos/${p.id}/editar`}>Editar</Link>
                                    {' · '}
                                    <button type="button" className="admin__danger" onClick={() => remove(p)}>
                                        Desactivar
                                    </button>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
        </AdminLayout>
    );
}