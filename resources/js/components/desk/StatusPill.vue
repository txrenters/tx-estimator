<script setup lang="ts">
import { computed } from 'vue';
import type { StatusValue } from '@/types/desk';

const props = defineProps<{ status: StatusValue }>();

const labels: Record<StatusValue, string> = {
    draft: 'Draft',
    pending: 'Pending approval',
    approved: 'Approved',
    declined: 'Declined',
};

const cls = computed(() =>
    props.status === 'pending' ? 'pending-approval' : props.status,
);
</script>

<template>
    <span class="badge" :class="cls"><i />{{ labels[status] }}</span>
</template>
