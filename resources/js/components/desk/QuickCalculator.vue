<script setup lang="ts">
import { ArrowRight, Delete, RotateCcw } from '@lucide/vue';
import { nextTick, onMounted, ref } from 'vue';
import { CALC_FIELDS, qtyText, useCalculator } from '@/composables/useCalculator';
import type { CalcField } from '@/composables/useCalculator';
import { money } from '@/lib/format';

const { variant = 'page', autofocus = true } = defineProps<{ variant?: 'page' | 'compact'; autofocus?: boolean }>();

const calc = useCalculator();
const inputs = ref<Record<CalcField, HTMLInputElement | null>>({ qty: null, price: null, extra: null });

// On touch screens the on-screen keypad replaces the phone keyboard.
const touch = typeof window !== 'undefined' && window.matchMedia('(pointer: coarse)').matches;

function focusField(key: CalcField) {
    calc.active.value = key;
    calc.fresh.value = true;
    inputs.value[key]?.focus();
    inputs.value[key]?.select();
}

function refocus() {
    inputs.value[calc.active.value]?.focus();
}

onMounted(() => {
    if (autofocus) {
        focusField('qty');
    }
});

function onFocus(key: CalcField, event: FocusEvent) {
    calc.active.value = key;
    calc.fresh.value = true;
    (event.target as HTMLInputElement).select();
}

function next() {
    calc.equals();
    const order: CalcField[] = ['qty', 'price', 'extra'];
    focusField(order[(order.indexOf(calc.active.value) + 1) % order.length]!);
}

function onKeydown(event: KeyboardEvent) {
    if (event.key === 'Enter' || event.key === '=') {
        event.preventDefault();
        next();
    }
}

async function clearAll() {
    calc.reset();
    await nextTick();
    focusField('qty');
}

const run = (fn: () => void) => () => {
    fn();
    refocus();
};

const keys: { label: string; kind: 'digit' | 'op' | 'fn'; action: () => void; aria?: string }[] = [
    ...['7', '8', '9'].map((d) => ({ label: d, kind: 'digit' as const, action: run(() => calc.press(d)) })),
    { label: '÷', kind: 'op', action: run(() => calc.press('÷')), aria: 'Divide' },
    ...['4', '5', '6'].map((d) => ({ label: d, kind: 'digit' as const, action: run(() => calc.press(d)) })),
    { label: '×', kind: 'op', action: run(() => calc.press('×')), aria: 'Multiply' },
    ...['1', '2', '3'].map((d) => ({ label: d, kind: 'digit' as const, action: run(() => calc.press(d)) })),
    { label: '−', kind: 'op', action: run(() => calc.press('−')), aria: 'Minus' },
    { label: '0', kind: 'digit', action: run(() => calc.press('0')) },
    { label: '.', kind: 'digit', action: run(() => calc.press('.')), aria: 'Decimal point' },
    { label: '=', kind: 'fn', action: run(calc.equals), aria: 'Equals' },
    { label: '+', kind: 'op', action: run(() => calc.press('+')), aria: 'Plus' },
];

defineExpose({ focusFirst: () => focusField('qty') });
</script>

<template>
    <div class="quick-calc" :class="variant">
        <div class="calc-body">
            <div class="calc-left">
                <div class="fields one calc-fields">
                    <label v-for="f in CALC_FIELDS" :key="f.key" :class="{ 'calc-active': calc.active.value === f.key }">
                        {{ f.label }}
                        <div class="unit-input">
                            <span v-if="f.unit">{{ f.unit }}</span>
                            <input
                                :ref="(el) => (inputs[f.key] = el as HTMLInputElement | null)"
                                type="text"
                                :inputmode="touch ? 'none' : 'decimal'"
                                autocomplete="off"
                                :aria-label="f.label"
                                :value="calc.text[f.key]"
                                @focus="onFocus(f.key, $event)"
                                @input="calc.type(f.key, ($event.target as HTMLInputElement).value)"
                                @keydown="onKeydown"
                            />
                        </div>
                        <small v-if="calc.hint(f.key)" class="calc-hint">{{ calc.hint(f.key) }}</small>
                    </label>
                </div>
                <div v-if="variant === 'page'" class="actions calc-clear">
                    <button type="button" class="button secondary compact" @click="clearAll"><RotateCcw :size="14" /> Clear all</button>
                </div>
            </div>

            <div class="keypad" role="group" aria-label="Keypad" @mousedown.prevent>
                <button v-for="k in keys" :key="k.label" type="button" :class="k.kind" :aria-label="k.aria ?? k.label" @click="k.action">
                    {{ k.label }}
                </button>
                <button type="button" class="fn" aria-label="Clear field" @click="run(calc.clearField)()">C</button>
                <button type="button" class="fn" aria-label="Backspace" @click="run(calc.backspace)()"><Delete :size="18" /></button>
                <button type="button" class="next" @click="next">Next <ArrowRight :size="16" /></button>
            </div>
        </div>

        <div v-if="variant === 'compact'" class="quick-calc-result" aria-live="polite">
            <div class="total-row">
                <span>Total <small>· {{ qtyText(calc.adjusted.value) }} units</small></span>
                <strong>{{ money(calc.total.value) }}</strong>
            </div>
            <p class="workings">{{ calc.workings.value }}</p>
            <div class="quick-calc-actions">
                <button type="button" class="button secondary compact" @click="clearAll"><RotateCcw :size="14" /> Clear</button>
                <slot name="actions" />
            </div>
        </div>
    </div>
</template>
