<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { computed } from 'vue';
import StatusPill from '@/components/desk/StatusPill.vue';
import { money } from '@/lib/format';
import type { StatusValue } from '@/types/desk';

defineOptions({ layout: { crumb: 'Compare estimates' } });

interface Selected {
    id: number;
    title: string;
    number: string;
    company: string;
    service: string;
    status: StatusValue;
    cost: number;
    markup: number;
    discount: number;
    trip: number;
    total: number;
    margin: number | null;
    target: number;
}

const props = defineProps<{
    options: { id: number; title: string; number: string }[];
    selected: Selected[];
}>();

const ids = computed(() => props.selected.map((s) => s.id));

function toggle(id: number) {
    const next = ids.value.includes(id) ? ids.value.filter((x) => x !== id) : [...ids.value, id].slice(0, 3);
    router.get('/compare', { ids: next }, { preserveScroll: true, preserveState: true, replace: true });
}

function duplicate(id: number) {
    router.post(`/estimates/${id}/duplicate`);
}

const rows: [string, (e: Selected) => string][] = [
    ['Company', (e) => e.company],
    ['Service', (e) => e.service],
    ['Direct cost', (e) => money(e.cost)],
    ['Markup', (e) => `${e.markup}%`],
    ['Discount', (e) => money(e.discount)],
    ['Trip charge', (e) => money(e.trip)],
    ['Total', (e) => money(e.total)],
    ['Gross margin', (e) => (e.margin === null ? 'Needs pricing' : `${e.margin.toFixed(1)}%`)],
    ['Margin target', (e) => `${e.target}%`],
];
</script>

<template>
    <Head title="Compare estimates" />
    <div class="page-heading">
        <div>
            <span class="eyebrow">PRICING REVIEW</span>
            <h1>Compare estimates</h1>
            <p>Compare up to three saved estimates. Duplicate an estimate to price another option.</p>
        </div>
    </div>

    <section class="panel section">
        <h3>Choose estimates</h3>
        <div v-if="options.length" class="pick-list">
            <label v-for="o in options" :key="o.id">
                <input
                    type="checkbox"
                    :checked="ids.includes(o.id)"
                    :disabled="ids.length >= 3 && !ids.includes(o.id)"
                    @change="toggle(o.id)"
                />
                {{ o.title }} <small>{{ o.number }}</small>
            </label>
        </div>
        <p v-else class="muted">Save an estimate to start a comparison.</p>
    </section>

    <section v-if="selected.length" class="panel section compare-panel">
        <div class="scroll">
            <table>
                <thead>
                    <tr>
                        <th />
                        <th v-for="e in selected" :key="e.id">
                            {{ e.title }}<small>{{ e.number }}</small>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th>Approval</th>
                        <td v-for="e in selected" :key="e.id"><StatusPill :status="e.status" /></td>
                    </tr>
                    <tr v-for="[label, value] in rows" :key="label">
                        <th>{{ label }}</th>
                        <td v-for="e in selected" :key="e.id">{{ value(e) }}</td>
                    </tr>
                    <tr>
                        <th>Options</th>
                        <td v-for="e in selected" :key="e.id">
                            <Link :href="`/estimates/${e.id}/edit`" class="text-button">Open</Link>
                            <button type="button" class="button secondary compact" @click="duplicate(e.id)">Duplicate</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
</template>
