<script setup lang="ts">
import { initials } from '@/lib/format';
import type { CompanySummary } from '@/types/desk';

const { company, size = 'dot' } = defineProps<{
    company: CompanySummary;
    size?: 'dot' | 'badge' | 'logo';
}>();
</script>

<template>
    <img
        v-if="company.logoUrl && size !== 'dot'"
        :src="company.logoUrl"
        :alt="`${company.name} logo`"
        class="company-logo"
        :class="size"
    />
    <img
        v-else-if="company.logoUrl"
        :src="company.logoUrl"
        alt=""
        class="company-logo dot"
    />
    <span
        v-else-if="size === 'dot'"
        class="company-dot"
        :style="{ background: company.color }"
    />
    <span
        v-else
        class="workspace-avatar"
        :style="{ background: company.tone, color: company.color }"
        >{{ initials(company.short) }}</span
    >
</template>
