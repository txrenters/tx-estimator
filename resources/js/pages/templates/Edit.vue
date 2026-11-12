<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { ArrowLeft, Check, Trash2 } from '@lucide/vue';
import { computed, watch } from 'vue';
import LineItemsEditor from '@/components/desk/LineItemsEditor.vue';
import UnsavedChangesGuard from '@/components/desk/UnsavedChangesGuard.vue';
import type { DeskProps, Line } from '@/types/desk';

defineOptions({ layout: { crumb: 'Service templates' } });

const props = defineProps<{
    template: {
        id: number | null;
        title: string;
        service_type: string;
        scope: string;
        terms: string;
        company_id: number | null;
        lines: Line[];
    };
    markup: number;
}>();

const desk = computed(() => usePage().props.desk as DeskProps);
const needsCompany = computed(() => props.template.id === null && !desk.value.currentCompany);

const form = useForm({
    title: props.template.title,
    service_type: props.template.service_type,
    scope: props.template.scope,
    terms: props.template.terms,
    company_id: props.template.company_id ?? desk.value.companies[0]?.id ?? null,
    lines: props.template.lines,
});

const serviceOptions = computed(
    () => desk.value.companies.find((c) => c.id === form.company_id)?.services ?? desk.value.serviceTypes,
);

watch(serviceOptions, (options) => {
    if (!options.includes(form.service_type)) {
        form.service_type = options[0] ?? form.service_type;
    }
}, { immediate: true });

function save({ onSuccess, onError }: { onSuccess?: () => void; onError?: () => void } = {}) {
    const options = { onSuccess: () => onSuccess?.(), onError: () => onError?.() };
    if (props.template.id) {
        form.put(`/templates/${props.template.id}`, options);
    } else {
        form.post('/templates', options);
    }
}

function destroy() {
    if (props.template.id) {
        form.delete(`/templates/${props.template.id}`);
    }
}
</script>

<template>
    <Head :title="template.id ? 'Edit template' : 'New template'" />
    <Link href="/templates" class="back"><ArrowLeft :size="16" /> Back to templates</Link>
    <div class="page-heading">
        <div>
            <div class="eyebrow"><i />SERVICE CATALOG</div>
            <h1>{{ template.id ? form.title || 'Edit template' : 'New template' }}</h1>
            <p>Reuse scopes and update unit rates as your pricing changes.</p>
        </div>
        <div class="actions">
            <button v-if="template.id" type="button" class="button danger-outline" :disabled="form.processing" @click="destroy">
                <Trash2 :size="16" /> Delete
            </button>
            <button type="button" class="button primary" :disabled="form.processing" @click="save()">
                <Check :size="16" /> Save template
            </button>
        </div>
    </div>

    <section class="panel section">
        <h3>Template details</h3>
        <div class="fields">
            <label>
                Template title
                <input v-model="form.title" placeholder="e.g. Evaporator coil replacement" />
                <span v-if="form.errors.title" class="form-error">{{ form.errors.title }}</span>
            </label>
            <label>
                Service type
                <select v-model="form.service_type">
                    <option v-for="s in serviceOptions" :key="s">{{ s }}</option>
                </select>
                <span v-if="form.errors.service_type" class="form-error">{{ form.errors.service_type }}</span>
            </label>
            <label v-if="needsCompany">
                Company
                <select v-model="form.company_id">
                    <option v-for="c in desk.companies" :key="c.id" :value="c.id">{{ c.name }}</option>
                </select>
                <span v-if="form.errors.company_id" class="form-error">{{ form.errors.company_id }}</span>
            </label>
        </div>
    </section>

    <section class="panel section">
        <h3>Line items</h3>
        <p class="muted">Markup applies to unit costs. A fixed sell price overrides markup for that line.</p>
        <LineItemsEditor v-model="form.lines" :markup="markup" :errors="form.errors" />
    </section>

    <section class="panel section">
        <h3>Scope and terms</h3>
        <div class="fields one">
            <label>Proposal scope <textarea v-model="form.scope" /></label>
            <label>Proposal terms and exclusions <textarea v-model="form.terms" /></label>
        </div>
    </section>

    <UnsavedChangesGuard :dirty="form.isDirty" :save="save" />
</template>
