<template>
    <Layout>
        <Head title="Inicio" />
        <section
            v-if="slide"
            class="hero"
            :style="{ backgroundImage: `url(${slide.url})` }"
            role="img"
            :aria-label="slide.caption || undefined"
        >
            <div class="hero__overlay" />
        </section>

        <div class="container page">
            <CategoryNav :categories="categories" />
            <h1 class="page__title">{{ search ? `Resultados para “${search}”` : 'Productos' }}</h1>
            <div v-if="embedSrc" class="home-video">
                <iframe
                    :src="embedSrc"
                    :title="video.title || 'Video promocional'"
                    width="100%"
                    height="315"
                    frameborder="0"
                    allow="autoplay; encrypted-media"
                    allowfullscreen
                />
            </div>
            <ProductGrid :products="products" />
        </div>
    </Layout>
</template>

<script setup>
import { computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import Layout from '../components/Layout.vue';
import CategoryNav from '../components/CategoryNav.vue';
import ProductGrid from '../components/ProductGrid.vue';

const props = defineProps({
    products: { type: Array, default: () => [] },
    categories: { type: Array, default: () => [] },
    slides: { type: Array, default: () => [] },
    video: { type: Object, default: null },
    search: { type: String, default: '' },
});

const slide = computed(() => props.slides?.[0]);
const embedSrc = computed(() =>
    props.video
        ? `https://www.youtube.com/embed/${props.video.video_id}?autoplay=1&start=${props.video.start}&end=${props.video.end}`
        : null
);
</script>
