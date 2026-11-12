import { computed, reactive, ref } from 'vue';
import { evaluate, isExpression } from '@/lib/expression';
import { money } from '@/lib/format';
import { round2 } from '@/lib/pricing';

/** Event the estimate builder listens for to add the calculated line. */
export const CALCULATOR_LINE_EVENT = 'desk:calculator-line';

export type CalcField = 'qty' | 'price' | 'extra';

export const CALC_FIELDS: { key: CalcField; label: string; unit?: string }[] = [
    { key: 'qty', label: 'Base quantity' },
    { key: 'price', label: 'Unit cost', unit: '$' },
    { key: 'extra', label: 'Waste or overage', unit: '%' },
];

// Module-level state: the Calculator page and the floating calculator share the same numbers,
// and values survive navigation between pages.
const text = reactive<Record<CalcField, string>>({
    qty: '1',
    price: '0',
    extra: '0',
});
const active = ref<CalcField>('qty');
// After a field gets focus, the first key press replaces its value (like selecting all).
const fresh = ref(true);

const value = (key: CalcField) => Math.min(100000000, evaluate(text[key]) ?? 0);

export const qtyText = (n: number) =>
    n.toLocaleString('en-US', { maximumFractionDigits: 2 });

// Same formulas as the original desk: N(qty × (1 + extra/100)) and N(qty × (1 + extra/100) × price).
const adjusted = computed(() =>
    round2(value('qty') * (1 + value('extra') / 100)),
);
const total = computed(() =>
    round2(value('qty') * (1 + value('extra') / 100) * value('price')),
);
const unitCost = computed(() => value('price'));

const workings = computed(() => {
    const qty = value('qty');
    const extra = value('extra');
    const base = extra
        ? `${qtyText(qty)} + ${qtyText(extra)}% = ${qtyText(adjusted.value)}`
        : qtyText(qty);

    return `${base} × ${money(value('price'))} = ${money(total.value)}`;
});

function hint(key: CalcField): string | null {
    if (!isExpression(text[key])) {
        return null;
    }
    const v = evaluate(text[key]);

    return v === null ? 'Check this sum' : `= ${qtyText(v)}`;
}

function press(key: string) {
    const field = active.value;
    const current = text[field];
    const isOperator = ['+', '−', '×', '÷'].includes(key);

    if (fresh.value && !isOperator) {
        text[field] = key === '.' ? '0.' : key;
    } else if (isOperator && /[+−×÷\-*/]$/.test(current)) {
        text[field] = current.slice(0, -1) + key; // swap a trailing operator
    } else {
        text[field] = current + key;
    }
    fresh.value = false;
}

function backspace() {
    text[active.value] = fresh.value ? '' : text[active.value].slice(0, -1);
    fresh.value = false;
}

function clearField() {
    text[active.value] = '';
    fresh.value = false;
}

/** "=" collapses a sum in the active field into its result. */
function equals() {
    const v = evaluate(text[active.value]);
    if (v !== null) {
        text[active.value] = String(round2(v));
    }
    fresh.value = true;
}

function type(key: CalcField, raw: string) {
    // Only keep characters the calculator understands.
    text[key] = raw.replace(/[^0-9.+\-−×÷*/ ]/g, '');
    fresh.value = false;
}

function reset() {
    Object.assign(text, { qty: '1', price: '0', extra: '0' });
}

export function useCalculator() {
    return {
        text,
        active,
        fresh,
        adjusted,
        total,
        unitCost,
        workings,
        hint,
        press,
        backspace,
        clearField,
        equals,
        type,
        reset,
    };
}
