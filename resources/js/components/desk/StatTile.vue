<script setup lang="ts">
import type { Component } from 'vue';

defineProps<{
    label: string;
    value: string | number;
    hint: string;
    icon: Component;
    tone?: 'green' | 'orange' | 'blue' | 'purple';
}>();
</script>

<template>
    <article class="panel metric">
        <div>
            <span>{{ label }}</span>
            <span class="metric-icon" :class="tone ?? 'green'">
                <component :is="icon" :size="17" />
            </span>
        </div>
        <strong>{{ value }}</strong>
        <small>{{ hint }}</small>
    </article>
</template>
