<script setup lang="ts">
import { serviceIcon } from '@/lib/serviceIcons';
import type { CompanySummary } from '@/types/desk';

defineProps<{ company: CompanySummary }>();
</script>

<template>
    <span v-if="company.logoUrl" class="brand-logo">
        <img :src="company.logoUrl" :alt="`${company.name} logo`" />
    </span>
    <span
        v-else
        class="brand-icon"
        :style="{
            '--company-tone': company.tone,
            '--company-color': company.color,
        }"
    >
        <component :is="serviceIcon(company.icon)" :size="18" />
    </span>
</template>
