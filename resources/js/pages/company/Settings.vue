<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { Check, ImageUp, Trash2 } from '@lucide/vue';
import { computed, ref } from 'vue';
import { serviceIcon } from '@/lib/serviceIcons';
import UnsavedChangesGuard from '@/components/desk/UnsavedChangesGuard.vue';
import type { CompanySummary, DeskProps } from '@/types/desk';

defineOptions({ layout: { crumb: 'Company settings' } });

const props = defineProps<{
    company: CompanySummary & { defaultScope: string; defaultTerms: string };
    serviceGroups: { name: string; icon: string; color: string; services: string[] }[];
}>();

const form = useForm<{
    name: string;
    short_name: string;
    location: string;
    color: string;
    logo: File | null;
    remove_logo: boolean;
    default_scope: string;
    default_terms: string;
    services: string[];
}>({
    name: props.company.name,
    short_name: props.company.short,
    location: props.company.location,
    color: props.company.color,
    logo: null,
    remove_logo: false,
    default_scope: props.company.defaultScope,
    default_terms: props.company.defaultTerms,
    services: [...props.company.services],
});

const canManageCatalog = computed(() => (usePage().props.desk as DeskProps).can.manageCompanies);

// Show only this company's own trades unless asked; based on what's saved, so a trade
// doesn't vanish while you're unticking its services.
const showAllTrades = ref(false);
const ownTrades = computed(() => props.serviceGroups.filter((g) => g.services.some((s) => props.company.services.includes(s))));
const otherTradeCount = computed(() => props.serviceGroups.length - ownTrades.value.length);
const visibleTrades = computed(() => (showAllTrades.value ? props.serviceGroups : ownTrades.value));

function toggleService(service: string) {
    if (form.services.includes(service)) {
        if (form.services.length > 1) {
            form.services = form.services.filter((s) => s !== service);
        }
    } else {
        form.services = [...form.services, service];
    }
}

const preview = ref<string | null>(null);
const shownLogo = computed(() => (form.remove_logo ? null : preview.value ?? props.company.logoUrl));

function pick(event: Event) {
    const file = (event.target as HTMLInputElement).files?.[0] ?? null;
    form.logo = file;
    form.remove_logo = false;
    preview.value = file ? URL.createObjectURL(file) : null;
}

function removeLogo() {
    form.logo = null;
    form.remove_logo = true;
    preview.value = null;
}

function save({ onSuccess, onError }: { onSuccess?: () => void; onError?: () => void } = {}) {
    form.post('/company/settings', {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            form.logo = null;
            form.remove_logo = false;
            preview.value = null;
            form.defaults();
            onSuccess?.();
        },
        onError: () => onError?.(),
    });
}
</script>

<template>
    <Head title="Company settings" />
    <div class="page-heading">
        <div>
            <span class="eyebrow">COMPANY SETTINGS</span>
            <h1>{{ company.name }}</h1>
            <p>Branding and proposal defaults for this company.</p>
        </div>
        <div class="actions">
            <button type="button" class="button primary" :disabled="form.processing" @click="save()">
                <Check :size="16" /> Save settings
            </button>
        </div>
    </div>

    <section class="panel section">
        <h3>Logo</h3>
        <p class="muted">Shown in the sidebar and on proposals. PNG, JPG or WebP, up to 2 MB.</p>
        <div class="logo-row">
            <div class="logo-preview" :style="{ background: company.tone }">
                <img v-if="shownLogo" :src="shownLogo" :alt="`${company.name} logo`" />
                <span v-else :style="{ color: form.color }">{{ form.short_name.slice(0, 2).toUpperCase() }}</span>
            </div>
            <label class="button secondary compact">
                <ImageUp :size="15" /> Upload logo
                <input type="file" accept="image/png,image/jpeg,image/webp" class="sr-only" @change="pick" />
            </label>
            <button v-if="shownLogo" type="button" class="button secondary compact" @click="removeLogo">
                <Trash2 :size="15" /> Remove
            </button>
        </div>
        <span v-if="form.errors.logo" class="form-error">{{ form.errors.logo }}</span>
        <progress v-if="form.progress" :value="form.progress.percentage" max="100" />
    </section>

    <section class="panel section">
        <h3>Company details</h3>
        <div class="fields">
            <label>
                Company name
                <input v-model="form.name" />
                <span v-if="form.errors.name" class="form-error">{{ form.errors.name }}</span>
            </label>
            <label>
                Short name
                <input v-model="form.short_name" />
                <span v-if="form.errors.short_name" class="form-error">{{ form.errors.short_name }}</span>
            </label>
            <label>
                Location
                <input v-model="form.location" placeholder="Greater Houston, TX" />
                <span v-if="form.errors.location" class="form-error">{{ form.errors.location }}</span>
            </label>
            <label>
                Brand color
                <div class="unit-input">
                    <input v-model="form.color" type="color" aria-label="Brand color" class="color-input" />
                    <input v-model="form.color" aria-label="Brand color hex" />
                </div>
                <span v-if="form.errors.color" class="form-error">{{ form.errors.color }}</span>
            </label>
        </div>
    </section>

    <section class="panel section">
        <h3>Services</h3>
        <p class="muted">
            What this company offers. Estimates and templates can only use these services.
            <Link v-if="canManageCatalog" href="/services" class="text-button">Manage the service catalog</Link>
        </p>
        <div class="service-trades">
            <div v-for="group in visibleTrades" :key="group.name" class="service-trade">
                <div class="service-trade-head">
                    <span class="brand-icon" :style="{ '--company-color': group.color, '--company-tone': `color-mix(in srgb, ${group.color} 12%, white)` }">
                        <component :is="serviceIcon(group.icon)" :size="16" />
                    </span>
                    <strong>{{ group.name }}</strong>
                    <small>{{ group.services.filter((s) => form.services.includes(s)).length }}/{{ group.services.length }}</small>
                </div>
                <div class="pick-list">
                    <label v-for="s in group.services" :key="s" :class="{ on: form.services.includes(s) }">
                        <input
                            type="checkbox"
                            :checked="form.services.includes(s)"
                            :disabled="form.services.length === 1 && form.services.includes(s)"
                            @change="toggleService(s)"
                        />
                        {{ s }}
                    </label>
                </div>
            </div>
        </div>
        <button
            v-if="otherTradeCount > 0"
            type="button"
            class="text-button trades-toggle"
            @click="showAllTrades = !showAllTrades"
        >
            <template v-if="showAllTrades">Show only {{ company.short }} trades</template>
            <template v-else>+ Add services from another trade ({{ otherTradeCount }})</template>
        </button>
        <span v-if="form.errors.services" class="form-error">{{ form.errors.services }}</span>
    </section>

    <section class="panel section">
        <h3>Proposal defaults</h3>
        <p class="muted">Used for new estimates when the template has no scope or terms of its own.</p>
        <div class="fields one">
            <label>Default proposal scope <textarea v-model="form.default_scope" /></label>
            <label>Default terms and exclusions <textarea v-model="form.default_terms" /></label>
        </div>
    </section>

    <UnsavedChangesGuard :dirty="form.isDirty" :save="save" />
</template>
