import ProductCard from './ProductCard';

export default function ProductGrid({ products }) {
    if (!products?.length) {
        return <p className="store-empty">No hay productos que mostrar.</p>;
    }

    return (
        <div className="product-grid">
            {products.map((p) => (
                <ProductCard key={p.id} product={p} />
            ))}
        </div>
    );
}
