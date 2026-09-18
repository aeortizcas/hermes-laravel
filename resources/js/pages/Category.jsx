import Layout from '../components/Layout';
import CategoryNav from '../components/CategoryNav';
import ProductGrid from '../components/ProductGrid';
import { Head } from '@inertiajs/react';

export default function Category({ category, products, categories }) {
    return (
        <Layout>
            <Head title={category.name} />
            <div className="container page">
                <CategoryNav categories={categories} activeId={category.id} />
                <h1 className="page__title">{category.name}</h1>
                <ProductGrid products={products} />
            </div>
        </Layout>
    );
}
