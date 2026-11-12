<script setup lang="ts">
import type { Pricing } from '@/types/desk';

const pricing = defineModel<Pricing>({ required: true });
const {
    readonly = false,
    errors = {},
    prefix = '',
} = defineProps<{
    readonly?: boolean;
    errors?: Record<string, string>;
    prefix?: string;
}>();

const fields: [keyof Pricing, string, string][] = [
    ['markup', 'Cost markup', '%'],
    ['discount', 'Subtotal discount', '%'],
    ['trip', 'Trip charge', '$'],
    ['authorization', 'Authorization limit', '$'],
    ['manager', 'Manager review threshold', '$'],
    ['target', 'Target gross margin', '%'],
];

function set(key: keyof Pricing, raw: string) {
    const max = key === 'discount' || key === 'target' ? 100 : 100000000;
    const value = raw === '' ? 0 : Math.min(max, Math.max(0, Number(raw) || 0));
    pricing.value = { ...pricing.value, [key]: value };
}
</script>

<template>
    <div class="fields pricing-fields">
        <label v-for="[key, label, unit] in fields" :key="key">
            {{ label }}
            <div class="unit-input">
                <span>{{ unit }}</span>
                <input
                    type="number"
                    min="0"
                    step="any"
                    :max="
                        key === 'discount' || key === 'target' ? 100 : undefined
                    "
                    :aria-label="label"
                    :value="pricing[key]"
                    :disabled="readonly"
                    @input="set(key, ($event.target as HTMLInputElement).value)"
                />
            </div>
            <span v-if="errors[prefix + key]" class="form-error">{{
                errors[prefix + key]
            }}</span>
        </label>
    </div>
</template>
