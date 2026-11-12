<script setup lang="ts">
import { Plus, Trash2 } from '@lucide/vue';
import { lineSell } from '@/lib/pricing';
import { money } from '@/lib/format';
import type { Line } from '@/types/desk';

const lines = defineModel<Line[]>({ required: true });
const { markup, readonly = false, errors = {} } = defineProps<{
    markup?: number;
    readonly?: boolean;
    errors?: Record<string, string>;
}>();

const blank = (): Line => ({ name: '', qty: 1, unit: 'job', labor: 0, materials: 0, fixed: null });

function num(value: string, max = 100000000): number {
    return Math.min(max, Math.max(0, Number(value) || 0));
}

function update(index: number, patch: Partial<Line>) {
    lines.value = lines.value.map((line, i) => (i === index ? { ...line, ...patch } : line));
}

function remove(index: number) {
    lines.value = lines.value.filter((_, i) => i !== index);
}

function lineError(index: number): string | undefined {
    const prefix = `lines.${index}.`;
    const key = Object.keys(errors).find((k) => k.startsWith(prefix));

    return key ? errors[key] : undefined;
}
</script>

<template>
    <div class="scroll">
        <table class="line-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Unit</th>
                    <th>Labor / unit</th>
                    <th>Materials / unit</th>
                    <th>Fixed sell / unit</th>
                    <th v-if="markup !== undefined">Sell total</th>
                    <th />
                </tr>
            </thead>
            <tbody>
                <template v-for="(line, i) in lines" :key="i">
                    <tr>
                        <td>
                            <input
                                :value="line.name"
                                placeholder="Work or material"
                                aria-label="Item"
                                :disabled="readonly"
                                @input="update(i, { name: ($event.target as HTMLInputElement).value })"
                            />
                        </td>
                        <td>
                            <input
                                type="number" min="0" step="any" aria-label="Qty"
                                :value="line.qty" :disabled="readonly"
                                @input="update(i, { qty: num(($event.target as HTMLInputElement).value, 1000000) })"
                            />
                        </td>
                        <td>
                            <input
                                :value="line.unit" aria-label="Unit" :disabled="readonly"
                                @input="update(i, { unit: ($event.target as HTMLInputElement).value })"
                            />
                        </td>
                        <td>
                            <input
                                type="number" min="0" step="any" aria-label="Labor / unit"
                                :value="line.labor" :disabled="readonly"
                                @input="update(i, { labor: num(($event.target as HTMLInputElement).value) })"
                            />
                        </td>
                        <td>
                            <input
                                type="number" min="0" step="any" aria-label="Materials / unit"
                                :value="line.materials" :disabled="readonly"
                                @input="update(i, { materials: num(($event.target as HTMLInputElement).value) })"
                            />
                        </td>
                        <td>
                            <input
                                type="number" min="0" step="any" aria-label="Fixed sell / unit"
                                :value="line.fixed ?? ''" :disabled="readonly"
                                @input="update(i, { fixed: ($event.target as HTMLInputElement).value === '' ? null : num(($event.target as HTMLInputElement).value) })"
                            />
                        </td>
                        <td v-if="markup !== undefined">{{ money(lineSell(line, markup)) }}</td>
                        <td>
                            <button
                                v-if="!readonly && lines.length > 1"
                                type="button" class="icon-button" aria-label="Remove line"
                                @click="remove(i)"
                            >
                                <Trash2 :size="15" />
                            </button>
                        </td>
                    </tr>
                    <tr v-if="lineError(i)">
                        <td colspan="8" class="form-error">{{ lineError(i) }}</td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
    <button v-if="!readonly" type="button" class="button secondary compact" @click="lines = [...lines, blank()]">
        <Plus :size="15" /> Add line
    </button>
</template>
