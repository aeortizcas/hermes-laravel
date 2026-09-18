import { useState } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import Layout from '../components/Layout';
import CategoryNav from '../components/CategoryNav';
import Price from '../components/Price';

export default function Product({ product, categories }) {
    const [activeImage, setActiveImage] = useState(0);
    const { data, setData, transform, post, processing, errors } = useForm({ qty: 1 });

    const images = product.images?.length ? product.images : product.cover ? [product.cover] : [];

    function addToCart(e) {
        e.preventDefault();
        transform((values) => ({ ...values, product_id: product.id }));
        post('/carrito/agregar', { preserveScroll: true });
    }

    return (
        <Layout>
            <Head title={product.name} />
            <div className="container page">
                <CategoryNav categories={categories} activeId={product.category?.id ?? null} />

                <div className="product-detail">
                    <div className="product-detail__gallery">
                        {images[activeImage] ? (
                            <img
                                className="product-detail__main-image"
                                src={images[activeImage]}
                                alt={product.name}
                            />
                        ) : (
                            <div className="product-detail__no-image">Sin imagen</div>
                        )}
                        {images.length > 1 && (
                            <div className="product-detail__thumbs">
                                {images.map((src, i) => (
                                    <button
                                        key={src + i}
                                        type="button"
                                        className={i === activeImage ? 'is-active' : ''}
                                        onClick={() => setActiveImage(i)}
                                        aria-label={`Imagen ${i + 1}`}
                                    >
                                        <img src={src} alt="" />
                                    </button>
                                ))}
                            </div>
                        )}
                    </div>

                    <div className="product-detail__info">
                        {product.category && (
                            <Link className="product-detail__category" href={`/categorias/${product.category.id}`}>
                                {product.category.name}
                            </Link>
                        )}
                        <h1 className="product-detail__title">{product.name}</h1>
                        {product.description && (
                            <p className="product-detail__description">{product.description}</p>
                        )}

                        {product.available ? (
                            <>
                                <p className="product-detail__price">
                                    <Price value={product.price} />
                                </p>
                                <p className="product-detail__stock">
                                    {product.stock <= 10
                                        ? `Solo quedan ${product.stock} unidades`
                                        : `En stock · ${product.stock} unidades disponibles`}
                                </p>

                                <form className="product-detail__buy" onSubmit={addToCart}>
                                    <label htmlFor="qty">Cantidad</label>
                                    <input
                                        id="qty"
                                        type="number"
                                        min="1"
                                        max={Math.min(99, product.stock)}
                                        value={data.qty}
                                        onChange={(e) => setData('qty', e.target.value)}
                                    />
                                    {errors.qty && <p className="form-error">{errors.qty}</p>}
                                    <button type="submit" disabled={processing}>
                                        {processing ? 'Agregando…' : 'Agregar al carrito'}
                                    </button>
                                </form>
                            </>
                        ) : (
                            <p className="product-detail__soldout">Producto agotado (AGOTADO)</p>
                        )}
                    </div>
                </div>
            </div>
        </Layout>
    );
}
