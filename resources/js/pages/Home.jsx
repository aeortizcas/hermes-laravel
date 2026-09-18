import Layout from '../components/Layout';
import CategoryNav from '../components/CategoryNav';
import ProductGrid from '../components/ProductGrid';
import { Head } from '@inertiajs/react';

export default function Home({ products, categories, slides, video, search }) {
    const slide = slides?.[0];
    const embedSrc = video
        ? `https://www.youtube.com/embed/${video.video_id}?autoplay=1&start=${video.start}&end=${video.end}`
        : null;

    return (
        <Layout>
            <Head title="Inicio" />
            {slide && (
                <section
                    className="hero"
                    style={{ backgroundImage: `url(${slide.url})` }}
                    role="img"
                    aria-label={slide.caption || undefined}
                >
                    <div className="hero__overlay" />
                </section>
            )}

            <div className="container page">
                <CategoryNav categories={categories} />
                <h1 className="page__title">
                    {search ? `Resultados para “${search}”` : 'Productos'}
                </h1>
                {embedSrc && (
                    <div className="home-video">
                        <iframe
                            src={embedSrc}
                            title={video.title || 'Video promocional'}
                            width="100%"
                            height="315"
                            frameBorder="0"
                            allow="autoplay; encrypted-media"
                            allowFullScreen
                        />
                    </div>
                )}
                <ProductGrid products={products} />
            </div>
        </Layout>
    );
}
