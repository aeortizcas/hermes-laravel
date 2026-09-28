<template>
    <nav v-if="categories?.length" class="category-nav" aria-label="Categorías">
        <Link href="/" :class="activeId === null ? 'is-active' : ''">Todos</Link>
        <Link
            v-for="c in categories"
            :key="c.id"
            :href="`/categorias/${c.id}`"
            :class="activeId === c.id ? 'is-active' : ''"
        >
            {{ c.name }}
        </Link>
    </nav>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    categories: { type: Array, default: () => [] },
    activeId: { type: [Number, String], default: null },
});
</script>
