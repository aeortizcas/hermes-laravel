import './css/app.css';
import { createInertiaApp } from '@inertiajs/vue3';
import { createApp, h } from 'vue';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';

const appName = import.meta.env.VITE_APP_NAME || 'Hermes';

createInertiaApp({
    title: (title) => (title ? `${title} · ${appName}` : appName),
    resolve: (name) =>
        resolvePageComponent(
            [`./pages/${name}.vue`, `./pages/${name}/index.vue`],
            import.meta.glob('./pages/**/*.vue')
        ),
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .mount(el);
    },
    progress: {
        delay: 250,
        color: '#c8102e',
    },
});
