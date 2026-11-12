<script setup lang="ts">
import { computed } from 'vue';
import { money } from '@/lib/format';
import { calculate } from '@/lib/pricing';
import type { Line, Pricing } from '@/types/desk';

const props = defineProps<{ lines: Line[]; pricing: Pricing }>();

const result = computed(() => calculate(props.lines, props.pricing));
const marginText = computed(() =>
    result.value.incomplete || !result.value.total ? '—' : `${result.value.margin.toFixed(1)}%`,
);
const belowTarget = computed(
    () => !result.value.incomplete && result.value.total > 0 && result.value.margin < props.pricing.target,
);
</script>

<template>
    <aside class="panel total-panel">
        <h3>Pricing summary</h3>
        <dl>
            <dt>Direct cost</dt>
            <dd>{{ money(result.cost) }}</dd>
            <dt>Line subtotal</dt>
            <dd>{{ money(result.subtotal) }}</dd>
            <dt>Discount</dt>
            <dd>−{{ money(result.discount) }}</dd>
            <dt>Trip charge</dt>
            <dd>{{ money(pricing.trip) }}</dd>
        </dl>
        <div class="total-row">
            <span>Estimate total</span>
            <strong>{{ money(result.total) }}</strong>
        </div>
        <div class="margin-row">
            <span>Gross margin</span>
            <strong>{{ marginText }}</strong>
        </div>
        <p>Target {{ pricing.target }}%{{ belowTarget ? ' · Below target' : '' }}</p>
        <p v-if="result.incomplete" class="attention">
            Enter direct costs for active lines to calculate margin. Check any zero-priced scope items before issuing.
        </p>
        <p v-if="result.total > pricing.authorization" class="attention">
            Above the {{ money(pricing.authorization) }} authorization limit.
        </p>
        <p v-if="result.total > pricing.manager" class="attention">Manager review threshold exceeded.</p>
        <p>Authorization flags are advisory. Margin is before overhead and tax.</p>
    </aside>
</template>
