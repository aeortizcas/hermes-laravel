<template>
    <div class="store">
        <Head title="Tienda" />
        <header class="store-header">
            <div class="store-header__row">
                <Link href="/" class="store-logo">
                    <span class="store-logo__text">her<em>mes</em></span>
                    <span class="store-logo__swoosh" aria-hidden="true" />
                </Link>

                <form class="store-search" role="search" @submit.prevent="submitSearch">
                    <input
                        v-model="search"
                        type="search"
                        name="search"
                        placeholder="Buscar productos…"
                        aria-label="Buscar productos"
                    />
                    <button type="submit" aria-label="Buscar">🔍</button>
                </form>

                <nav class="store-nav" aria-label="Cuenta">
                    <template v-if="auth">
                        <div class="store-nav__account" :title="auth.name">
                            <span class="store-nav__hello">Hola,</span>
                            <span class="store-nav__user">{{ auth.name }}</span>
                        </div>
                        <Link href="/mis-envios" class="store-nav__link">Mis envíos</Link>
                        <Link href="/suscripcion" class="store-nav__link">Membresía</Link>
                        <button type="button" class="store-nav__link" @click="logout">Salir</button>
                    </template>
                    <template v-else>
                        <Link href="/registro" class="store-nav__link">Registrarse</Link>
                        <Link href="/login" class="store-nav__link">Entrar</Link>
                    </template>
                    <Link href="/carrito" class="store-cart" aria-label="Ver carrito">
                        <span class="store-cart__icon" aria-hidden="true">🛒</span>
                        <span class="store-cart__label">Carrito</span>
                        <span v-if="cartCount > 0" class="store-cart__badge">{{ cartCount }}</span>
                    </Link>
                </nav>
            </div>

            <nav class="store-menu" aria-label="Menú principal">
                <Link href="/" class="store-menu__all">☰ Todos</Link>
                <div v-for="item in menu" :key="item.id" class="store-menu__item">
                    <span class="store-menu__name">{{ item.name }}</span>
                    <div v-if="item.submenus?.length > 0" class="store-menu__sub">
                        <a v-for="(s, i) in item.submenus" :key="i" :href="normalizeUrl(s.url)">
                            {{ s.name }}
                        </a>
                    </div>
                </div>
            </nav>
        </header>

        <div v-if="flash?.success" class="store-flash">{{ flash.success }}</div>
        <div v-if="flash?.error" class="store-flash store-flash--error">{{ flash.error }}</div>

        <main class="store-main">
            <slot />
        </main>

        <footer class="store-footer">
            <div class="store-footer__top container">
                <div class="store-footer__brand">
                    <strong>Hermes</strong>
                    <p>Tienda en línea</p>
                </div>
                <div class="store-footer__links">
                    <Link href="/">Catálogo</Link>
                    <Link href="/pedido">Hacer un pedido</Link>
                    <Link href="/mis-envios">Mis envíos</Link>
                    <Link href="/carrito">Carrito</Link>
                </div>
            </div>
            <div class="store-footer__bottom">
                © {{ year }} Hermes · Tienda en línea
            </div>
        </footer>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';

const page = usePage();
const search = ref(page.props.search ?? '');
const cartCount = page.props.cartCount ?? 0;
const flash = page.props.flash;
const auth = page.props.auth;
const menu = page.props.menu ?? [];
const year = new Date().getFullYear();

function submitSearch() {
    router.get('/', { search: search.value }, { preserveState: true, replace: true });
}

function logout() {
    router.post('/logout');
}

function normalizeUrl(url) {
    if (!url) return '#';
    const trimmed = url.trim();
    if (/^(f|ht)tps?:\/\//i.test(trimmed) || trimmed.startsWith('/')) return trimmed;
    return `https://${trimmed}`;
}
</script>
