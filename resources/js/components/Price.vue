<template>
    <span class="price" :class="className" :aria-label="label">
        <span class="price__symbol" aria-hidden="true">$</span>
        <span class="price__int">{{ int }}</span>
        <span class="price__cents" aria-hidden="true">.{{ cents }}</span>
    </span>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    value: { type: [Number, String], default: 0 },
    className: { type: String, default: '' },
});

const parts = computed(() => Number(props.value || 0).toFixed(2).split('.'));
const int = computed(() => parts.value[0]);
const cents = computed(() => parts.value[1]);
const label = computed(() => Number(props.value || 0).toFixed(2));
</script>
