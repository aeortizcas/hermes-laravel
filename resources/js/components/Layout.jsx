import { Head, Link, router, usePage } from '@inertiajs/react';

export default function Layout({ children }) {
    const { cartCount, flash, auth, menu } = usePage().props;
    const search = usePage().props.search ?? '';

    function submitSearch(e) {
        e.preventDefault();
        const q = new FormData(e.target).get('search') ?? '';
        router.get('/', { search: q }, { preserveState: true, replace: true });
    }

    function logout(e) {
        e.preventDefault();
        router.post('/logout');
    }

    return (
        <div className="store">
            <Head title="Tienda" />
            <header className="store-header">
                <div className="store-header__row">
                    <Link href="/" className="store-logo">
                        <span className="store-logo__text">
                            her<em>mes</em>
                        </span>
                        <span className="store-logo__swoosh" aria-hidden="true" />
                    </Link>

                    <form className="store-search" onSubmit={submitSearch} role="search">
                        <input
                            type="search"
                            name="search"
                            defaultValue={search}
                            placeholder="Buscar productos…"
                            aria-label="Buscar productos"
                        />
                        <button type="submit" aria-label="Buscar">
                            🔍
                        </button>
                    </form>

                    <nav className="store-nav" aria-label="Cuenta">
                        {auth ? (
                            <>
                                <div className="store-nav__account" title={auth.name}>
                                    <span className="store-nav__hello">Hola,</span>
                                    <span className="store-nav__user">{auth.name}</span>
                                </div>
                                <Link href="/mis-envios" className="store-nav__link">
                                    Mis envíos
                                </Link>
                                <Link href="/suscripcion" className="store-nav__link">
                                    Membresía
                                </Link>
                                <button type="button" className="store-nav__link" onClick={logout}>
                                    Salir
                                </button>
                            </>
                        ) : (
                            <>
                                <Link href="/registro" className="store-nav__link">
                                    Registrarse
                                </Link>
                                <Link href="/login" className="store-nav__link">
                                    Entrar
                                </Link>
                            </>
                        )}
                        <Link href="/carrito" className="store-cart" aria-label="Ver carrito">
                            <span className="store-cart__icon" aria-hidden="true">
                                🛒
                            </span>
                            <span className="store-cart__label">Carrito</span>
                            {cartCount > 0 && (
                                <span className="store-cart__badge">{cartCount}</span>
                            )}
                        </Link>
                    </nav>
                </div>

                <nav className="store-menu" aria-label="Menú principal">
                        <Link href="/" className="store-menu__all">
                            ☰ Todos
                        </Link>
                        {menu?.map((item) => (
                            <div className="store-menu__item" key={item.id}>
                                <span className="store-menu__name">{item.name}</span>
                                {item.submenus?.length > 0 && (
                                    <div className="store-menu__sub">
                                        {item.submenus.map((s, i) => (
                                            <a key={i} href={normalizeUrl(s.url)}>
                                                {s.name}
                                            </a>
                                        ))}
                                    </div>
                                )}
                            </div>
                        ))}
                    </nav>
            </header>

            {flash?.success && <div className="store-flash">{flash.success}</div>}
            {flash?.error && <div className="store-flash store-flash--error">{flash.error}</div>}

            <main className="store-main">{children}</main>

            <footer className="store-footer">
                <div className="store-footer__top container">
                    <div className="store-footer__brand">
                        <strong>Hermes</strong>
                        <p>Tienda en línea</p>
                    </div>
                    <div className="store-footer__links">
                        <Link href="/">Catálogo</Link>
                        <Link href="/pedido">Hacer un pedido</Link>
                        <Link href="/mis-envios">Mis envíos</Link>
                        <Link href="/carrito">Carrito</Link>
                    </div>
                </div>
                <div className="store-footer__bottom">
                    © {new Date().getFullYear()} Hermes · Tienda en línea
                </div>
            </footer>
        </div>
    );
}

function normalizeUrl(url) {
    if (!url) return '#';
    const trimmed = url.trim();
    if (/^(f|ht)tps?:\/\//i.test(trimmed) || trimmed.startsWith('/')) return trimmed;
    return `https://${trimmed}`;
}