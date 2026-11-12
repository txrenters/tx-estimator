<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { Calculator, ListPlus, X } from '@lucide/vue';
import { computed, nextTick, onBeforeUnmount, onMounted, ref } from 'vue';
import QuickCalculator from '@/components/desk/QuickCalculator.vue';
import {
    CALCULATOR_LINE_EVENT,
    useCalculator,
} from '@/composables/useCalculator';

const page = usePage();
const calc = useCalculator();
const open = ref(false);
const quick = ref<InstanceType<typeof QuickCalculator> | null>(null);
const added = ref(false);

// The Calculator page already shows the full calculator.
const hidden = computed(() => page.component === 'Calculator');
const onBuilder = computed(() => page.component === 'estimates/Builder');

async function toggle() {
    open.value = !open.value;
    added.value = false;
    if (open.value) {
        await nextTick();
        quick.value?.focusFirst();
    }
}

function addLine() {
    window.dispatchEvent(
        new CustomEvent(CALCULATOR_LINE_EVENT, {
            detail: {
                qty: calc.adjusted.value,
                materials: calc.unitCost.value,
            },
        }),
    );
    added.value = true;
}

function onKey(event: KeyboardEvent) {
    if (event.key === 'Escape' && open.value) {
        open.value = false;
    }
}

onMounted(() => window.addEventListener('keydown', onKey));
onBeforeUnmount(() => window.removeEventListener('keydown', onKey));
</script>

<template>
    <template v-if="!hidden">
        <section
            v-if="open"
            class="panel floating-calc"
            role="dialog"
            aria-label="Calculator"
        >
            <header class="floating-calc-head">
                <strong><Calculator :size="16" /> Calculator</strong>
                <button
                    type="button"
                    class="icon-button"
                    aria-label="Close calculator"
                    @click="open = false"
                >
                    <X :size="16" />
                </button>
            </header>
            <QuickCalculator ref="quick" variant="compact" :autofocus="false">
                <template #actions>
                    <button
                        v-if="onBuilder"
                        type="button"
                        class="button primary compact"
                        data-test="calc-add-line"
                        @click="addLine"
                    >
                        <ListPlus :size="15" />
                        {{ added ? 'Added — add again' : 'Add to line items' }}
                    </button>
                </template>
            </QuickCalculator>
        </section>
        <button
            type="button"
            class="calc-fab"
            :class="{ open }"
            :aria-label="open ? 'Close calculator' : 'Open calculator'"
            data-test="calc-fab"
            @click="toggle"
        >
            <X v-if="open" :size="22" />
            <Calculator v-else :size="22" />
        </button>
    </template>
</template>
