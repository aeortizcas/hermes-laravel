import { Head, Link, router, usePage } from '@inertiajs/react';

export default function AdminLayout({ children, title = 'Panel' }) {
    const { flash, admin } = usePage().props;

    function logout(e) {
        e.preventDefault();
        router.post('/admin/logout');
    }

    return (
        <div className="admin">
            <Head title={title} />
            <aside className="admin__side">
                <Link href="/admin" className="admin__logo">
                    Hermes Admin
                </Link>
                <nav className="admin__nav">
                    <Link href="/admin" className="admin__nav-link">
                        Dashboard
                    </Link>
                    <Link href="/admin/productos" className="admin__nav-link">
                        Productos
                    </Link>
                    <Link href="/admin/envios" className="admin__nav-link">
                        Envíos / pedidos
                    </Link>
                    <Link href="/admin/catalogos/categorias" className="admin__nav-link">
                        Categorías
                    </Link>
                    <Link href="/admin/catalogos/empresas" className="admin__nav-link">
                        Empresas de envío
                    </Link>
                    <Link href="/admin/catalogos/pagos" className="admin__nav-link">
                        Métodos de pago
                    </Link>
                    <Link href="/admin/catalogos/ciudades" className="admin__nav-link">
                        Ciudades
                    </Link>
                    <Link href="/admin/catalogos/bancos" className="admin__nav-link">
                        Bancos
                    </Link>
                    <Link href="/admin/catalogos/monedas" className="admin__nav-link">
                        Monedas
                    </Link>
                    <Link href="/admin/catalogos/unidades" className="admin__nav-link">
                        Unidades
                    </Link>
                    <Link href="/admin/catalogos/pesos" className="admin__nav-link">
                        Pesos
                    </Link>
                    <Link href="/admin/catalogos/proveedores" className="admin__nav-link">
                        Proveedores
                    </Link>
                    <Link href="/admin/catalogos/paises" className="admin__nav-link">
                        Países
                    </Link>
                    <Link href="/admin/catalogos/departamentos" className="admin__nav-link">
                        Departamentos
                    </Link>
                    <Link href="/admin/catalogos/protenvios" className="admin__nav-link">
                        Tarifas de envío
                    </Link>
                    <Link href="/admin/catalogos/menu_items" className="admin__nav-link">
                        Ítems de menú
                    </Link>
                    <Link href="/admin/catalogos/videos" className="admin__nav-link">
                        Videos
                    </Link>
                    <Link href="/admin/catalogos/suscripciones" className="admin__nav-link">
                        Suscripciones
                    </Link>
                    <Link href="/admin/catalogos/clientes" className="admin__nav-link">
                        Clientes
                    </Link>
                    <Link href="/admin/paginas" className="admin__nav-link">
                        Páginas / Niveles
                    </Link>
                    <Link href="/" className="admin__nav-link admin__nav-link--muted">
                        ← Ver tienda
                    </Link>
                </nav>
                {admin && (
                    <div className="admin__user">
                        <span>{admin.nombre || admin.usuario}</span>
                        <button type="button" onClick={logout}>
                            Cerrar sesión
                        </button>
                    </div>
                )}
            </aside>
            <main className="admin__main">
                {flash?.success && <div className="store-flash">{flash.success}</div>}
                {children}
            </main>
        </div>
    );
}