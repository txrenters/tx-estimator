<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Check } from '@lucide/vue';
import { computed, ref } from 'vue';
import PricingFields from '@/components/desk/PricingFields.vue';
import UnsavedChangesGuard from '@/components/desk/UnsavedChangesGuard.vue';
import type { CompanySummary, Pricing } from '@/types/desk';

defineOptions({ layout: { crumb: 'Pricing defaults' } });

const props = defineProps<{
    defaults: Pricing;
    company: CompanySummary | null;
    canEdit: boolean;
}>();

const pricing = ref<Pricing>({ ...props.defaults });
const saved = ref<Pricing>({ ...props.defaults });
const dirty = computed(
    () => JSON.stringify(pricing.value) !== JSON.stringify(saved.value),
);
const form = useForm<Pricing>({ ...props.defaults });
const companyError = computed(
    () => (form.errors as Record<string, string>).company_id,
);

function save({
    onSuccess,
    onError,
}: { onSuccess?: () => void; onError?: () => void } = {}) {
    form.transform(() => ({ ...pricing.value })).put('/pricing-defaults', {
        preserveScroll: true,
        onSuccess: () => {
            saved.value = { ...pricing.value };
            onSuccess?.();
        },
        onError: () => onError?.(),
    });
}
</script>

<template>
    <Head title="Pricing defaults" />
    <div class="page-heading">
        <div>
            <div class="eyebrow"><i />WORKSPACE SETTINGS</div>
            <h1>Pricing defaults</h1>
            <p v-if="company">
                Set starting values for new estimates at {{ company.name }}.
            </p>
            <p v-else>
                Choose a company in the sidebar to set its starting values for
                new estimates.
            </p>
        </div>
        <div v-if="canEdit" class="actions">
            <button
                type="button"
                class="button primary"
                :disabled="form.processing"
                @click="save()"
            >
                <Check :size="16" /> Save defaults
            </button>
        </div>
    </div>

    <p v-if="companyError" class="notice bad">
        {{ companyError }}
    </p>

    <section class="panel section">
        <h3>Default rates and thresholds</h3>
        <p class="muted">
            Saved estimates keep their pricing. You can override these defaults
            on any estimate.
        </p>
        <PricingFields
            v-model="pricing"
            :readonly="!canEdit"
            :errors="form.errors"
        />
        <p class="muted policy-note">
            Discount applies to the line subtotal. The trip charge is added
            afterward. Authorization thresholds are shown as flags on each
            estimate.
        </p>
    </section>

    <UnsavedChangesGuard :dirty="dirty && canEdit" :save="save" />
</template>
