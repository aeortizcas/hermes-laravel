import { Link, router } from '@inertiajs/react';
import Price from './Price';

export default function ProductCard({ product }) {
    function addToCart(e) {
        e.preventDefault();
        router.post('/carrito/agregar', { product_id: product.id, qty: 1 }, { preserveScroll: true });
    }

    return (
        <article className="product-card">
            <Link href={`/producto/${product.id}`} className="product-card__media">
                {product.cover ? (
                    <img src={product.cover} alt={product.name} loading="lazy" />
                ) : (
                    <div className="product-card__placeholder">Sin imagen</div>
                )}
            </Link>
            <div className="product-card__body">
                <h3 className="product-card__title">
                    <Link href={`/producto/${product.id}`}>{product.name}</Link>
                </h3>
                {product.available ? (
                    <>
                        <p className="product-card__price">
                            <Price value={product.price} />
                        </p>
                        <p className={`product-card__stock${product.stock <= 10 ? ' product-card__stock--low' : ''}`}>
                            {product.stock <= 10
                                ? `Solo quedan ${product.stock} unidades`
                                : 'En stock'}
                        </p>
                        <div className="product-card__add">
                            <button type="button" className="btn btn--primary btn--small" onClick={addToCart}>
                                Agregar al carrito
                            </button>
                        </div>
                    </>
                ) : (
                    <p className="product-card__soldout">Agotado</p>
                )}
            </div>
        </article>
    );
}