<template>
    <div class="admin">
        <Head :title="title" />
        <aside class="admin__side">
            <Link href="/admin" class="admin__logo">Hermes Admin</Link>
            <nav class="admin__nav">
                <Link href="/admin" class="admin__nav-link">Dashboard</Link>
                <Link href="/admin/productos" class="admin__nav-link">Productos</Link>
                <Link href="/admin/envios" class="admin__nav-link">Envíos / pedidos</Link>
                <Link href="/admin/catalogos/categorias" class="admin__nav-link">Categorías</Link>
                <Link href="/admin/catalogos/empresas" class="admin__nav-link">Empresas de envío</Link>
                <Link href="/admin/catalogos/pagos" class="admin__nav-link">Métodos de pago</Link>
                <Link href="/admin/catalogos/ciudades" class="admin__nav-link">Ciudades</Link>
                <Link href="/admin/catalogos/bancos" class="admin__nav-link">Bancos</Link>
                <Link href="/admin/catalogos/monedas" class="admin__nav-link">Monedas</Link>
                <Link href="/admin/catalogos/unidades" class="admin__nav-link">Unidades</Link>
                <Link href="/admin/catalogos/pesos" class="admin__nav-link">Pesos</Link>
                <Link href="/admin/catalogos/proveedores" class="admin__nav-link">Proveedores</Link>
                <Link href="/admin/catalogos/paises" class="admin__nav-link">Países</Link>
                <Link href="/admin/catalogos/departamentos" class="admin__nav-link">Departamentos</Link>
                <Link href="/admin/catalogos/protenvios" class="admin__nav-link">Tarifas de envío</Link>
                <Link href="/admin/catalogos/menu_items" class="admin__nav-link">Ítems de menú</Link>
                <Link href="/admin/catalogos/videos" class="admin__nav-link">Videos</Link>
                <Link href="/admin/catalogos/suscripciones" class="admin__nav-link">Suscripciones</Link>
                <Link href="/admin/catalogos/clientes" class="admin__nav-link">Clientes</Link>
                <Link href="/admin/paginas" class="admin__nav-link">Páginas / Niveles</Link>
                <Link href="/" class="admin__nav-link admin__nav-link--muted">← Ver tienda</Link>
            </nav>
            <div v-if="admin" class="admin__user">
                <span>{{ admin.nombre || admin.usuario }}</span>
                <button type="button" @click="logout">Cerrar sesión</button>
            </div>
        </aside>
        <main class="admin__main">
            <div v-if="flash?.success" class="store-flash">{{ flash.success }}</div>
            <slot />
        </main>
    </div>
</template>

<script setup>
import { Head, Link, router, usePage } from '@inertiajs/vue3';

defineProps({
    title: { type: String, default: 'Panel' },
});

const page = usePage();
const flash = page.props.flash;
const admin = page.props.admin;

function logout() {
    router.post('/admin/logout');
}
</script>
