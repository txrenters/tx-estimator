<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import QuickCalculator from '@/components/desk/QuickCalculator.vue';
import { qtyText, useCalculator } from '@/composables/useCalculator';
import { money } from '@/lib/format';

defineOptions({ layout: { crumb: 'Calculator' } });

const calc = useCalculator();
</script>

<template>
    <Head title="Calculator" />
    <div class="page-heading">
        <div>
            <span class="eyebrow">QUICK TOOLS</span>
            <h1>Quantity calculator</h1>
            <p>Calculate a line total with optional waste or overage.</p>
        </div>
    </div>

    <div class="calculator-grid">
        <section class="panel section">
            <h3>Quantity and unit cost</h3>
            <QuickCalculator variant="page" />
        </section>

        <aside class="panel total-panel calculator-result" aria-live="polite">
            <h3>Result</h3>
            <dl>
                <dt>Adjusted quantity</dt>
                <dd>{{ qtyText(calc.adjusted.value) }}</dd>
            </dl>
            <div class="total-row">
                <span>Calculated total</span>
                <strong>{{ money(calc.total.value) }}</strong>
            </div>
            <p class="workings">{{ calc.workings.value }}</p>
        </aside>
    </div>
</template>
