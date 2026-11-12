<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ArrowRight, Plus } from '@lucide/vue';
import { computed, reactive, watch } from 'vue';
import BrandIcon from '@/components/desk/BrandIcon.vue';
import type { CompanySummary, DeskProps } from '@/types/desk';

defineOptions({ layout: { crumb: 'Service templates' } });

const props = defineProps<{
    templates: {
        id: number;
        title: string;
        service: string;
        scope: string | null;
        company: CompanySummary;
        canEdit: boolean;
    }[];
    filters: { service: string; company: string | number; group?: string };
    canEdit: boolean;
}>();

const desk = computed(() => usePage().props.desk as DeskProps);
const filters = reactive({
    service: props.filters.service ?? '',
    company: props.filters.company ?? '',
});

watch(filters, () => {
    router.get(
        '/templates',
        {
            service: filters.service || undefined,
            company: filters.company || undefined,
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        },
    );
});
</script>

<template>
    <Head title="Service templates" />
    <div class="page-heading">
        <div>
            <div class="eyebrow"><i />SERVICE CATALOG</div>
            <h1>Service templates</h1>
            <p>Reuse scopes and update unit rates as your pricing changes.</p>
        </div>
        <div v-if="canEdit" class="actions">
            <Link href="/templates/create" class="button primary"
                ><Plus :size="16" /> New template</Link
            >
        </div>
    </div>

    <div class="template-filters">
        <select
            v-if="desk.can.switchCompany && !desk.currentCompany"
            v-model="filters.company"
            aria-label="Company"
        >
            <option value="">All companies</option>
            <option v-for="c in desk.companies" :key="c.id" :value="c.id">
                {{ c.name }}
            </option>
        </select>
        <select v-model="filters.service" aria-label="Service">
            <option value="">All services</option>
            <option
                v-for="s in desk.currentCompany?.services ?? desk.serviceTypes"
                :key="s"
            >
                {{ s }}
            </option>
        </select>
    </div>

    <div v-if="templates.length" class="template-grid">
        <article v-for="t in templates" :key="t.id" class="panel template-card">
            <div class="template-top">
                <BrandIcon :company="t.company" />
                <span
                    class="company-tag"
                    :style="{
                        '--company-tone': t.company.tone,
                        '--company-color': t.company.color,
                    }"
                >
                    {{ t.service }}
                </span>
            </div>
            <h3>{{ t.title }}</h3>
            <p>{{ t.scope || 'Custom service template.' }}</p>
            <div class="template-actions">
                <Link
                    v-if="t.canEdit"
                    :href="`/templates/${t.id}/edit`"
                    class="button secondary compact"
                    >Edit rates</Link
                >
                <span v-else />
                <Link
                    :href="`/estimates/create?template=${t.id}`"
                    class="text-button"
                    >Use template <ArrowRight :size="15"
                /></Link>
            </div>
        </article>
    </div>
    <div v-else class="panel empty">
        <h3>No templates</h3>
        <p>No templates match these filters.</p>
    </div>
</template>
