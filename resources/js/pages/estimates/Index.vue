<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import {
    BadgeCheck,
    ClipboardList,
    Clock3,
    FileText,
    GitCompareArrows,
    LayoutGrid,
    Plus,
    Search,
} from '@lucide/vue';
import { computed, reactive, ref, watch } from 'vue';
import StatTile from '@/components/desk/StatTile.vue';
import StatusPill from '@/components/desk/StatusPill.vue';
import { date, money } from '@/lib/format';
import type { CompanySummary, DeskProps, StatusValue } from '@/types/desk';

defineOptions({ layout: { crumb: 'Estimates' } });

interface Row {
    id: number;
    number: string;
    title: string;
    client: string | null;
    company: CompanySummary;
    status: StatusValue;
    total: number;
    updatedAt: string;
    flags: { belowTarget: boolean; aboveAuthorization: boolean; aboveManagerReview: boolean };
}

const props = defineProps<{
    estimates: {
        data: Row[];
        total: number;
        links: { url: string | null; label: string; active: boolean }[];
        last_page: number;
    };
    stats: { total: number; pending: number; approvedValue: number; templates: number };
    filters: { company: string | number; service: string; status: string; q: string };
}>();

const desk = computed(() => usePage().props.desk as DeskProps);
const showCompanyFilter = computed(() => desk.value.can.switchCompany && !desk.value.currentCompany);

const eyebrow = computed(() =>
    (desk.value.currentCompany?.location ?? 'Greater Houston').replace(/,.*$/, '').toUpperCase() + ' OPERATIONS',
);
const heading = computed(() => desk.value.currentCompany?.name ?? 'Estimating desk');

const filters = reactive({ ...props.filters });
let searchTimer: ReturnType<typeof setTimeout> | undefined;

watch(
    () => ({ ...filters }),
    (value, old) => {
        clearTimeout(searchTimer);
        const go = () =>
            router.get(
                '/estimates',
                Object.fromEntries(Object.entries(value).filter(([, v]) => v !== '' && v !== null)),
                { preserveState: true, preserveScroll: true, replace: true },
            );

        if (value.q !== old.q) {
            searchTimer = setTimeout(go, 250);
        } else {
            go();
        }
    },
);

const selected = ref<number[]>([]);

function toggle(id: number) {
    if (selected.value.includes(id)) {
        selected.value = selected.value.filter((x) => x !== id);
    } else if (selected.value.length < 3) {
        selected.value = [...selected.value, id];
    }
}

const compareHref = computed(() =>
    selected.value.length ? '/compare?' + selected.value.map((id) => `ids[]=${id}`).join('&') : '/compare',
);
</script>

<template>
    <Head title="Estimates" />
    <div class="page-heading dashboard-heading">
        <div>
            <div class="eyebrow"><i />{{ eyebrow }}</div>
            <h1>{{ heading }}</h1>
            <p>Build service pricing, track approvals, and turn estimates into proposals.</p>
        </div>
        <Link href="/estimates/create" class="button primary"><Plus :size="19" /> New estimate</Link>
    </div>

    <div class="metrics">
        <StatTile label="Total estimates" :value="stats.total" hint="Estimates in this view" :icon="ClipboardList" tone="green" />
        <StatTile label="Pending approval" :value="stats.pending" hint="Awaiting a decision" :icon="Clock3" tone="orange" />
        <StatTile label="Approved value" :value="money(stats.approvedValue)" hint="Total approved estimates" :icon="BadgeCheck" tone="blue" />
        <StatTile label="Service templates" :value="stats.templates" hint="Reusable scopes and rates" :icon="LayoutGrid" tone="purple" />
    </div>

    <section class="panel">
        <div class="table-heading">
            <div>
                <h2>Estimates</h2>
                <p>Manage service quotes across your companies.</p>
            </div>
            <Link :href="compareHref" class="button secondary compact">
                <GitCompareArrows :size="16" /> Compare{{ selected.length ? ` (${selected.length})` : '' }}
            </Link>
        </div>
        <div class="filters">
            <label class="search">
                <Search :size="17" />
                <input v-model="filters.q" aria-label="Search estimates" placeholder="Search estimates…" />
            </label>
            <div class="selects">
                <select v-if="showCompanyFilter" v-model="filters.company" aria-label="Company filter">
                    <option value="">All companies</option>
                    <option v-for="c in desk.companies" :key="c.id" :value="c.id">{{ c.name }}</option>
                </select>
                <select v-model="filters.service" aria-label="Service filter">
                    <option value="">All services</option>
                    <option v-for="s in desk.currentCompany?.services ?? desk.serviceTypes" :key="s">{{ s }}</option>
                </select>
                <select v-model="filters.status" aria-label="Status filter">
                    <option value="">All statuses</option>
                    <option v-for="s in desk.statuses" :key="s.value" :value="s.value">{{ s.label }}</option>
                </select>
            </div>
        </div>

        <div class="scroll">
            <table class="estimate-table">
                <thead>
                    <tr>
                        <th class="check-col"><GitCompareArrows :size="14" /></th>
                        <th>Estimate</th>
                        <th>Company</th>
                        <th>Status</th>
                        <th>Total</th>
                        <th>Updated</th>
                        <th />
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="e in estimates.data" :key="e.id">
                        <td>
                            <input
                                type="checkbox"
                                :aria-label="`Compare ${e.title}`"
                                :checked="selected.includes(e.id)"
                                :disabled="!selected.includes(e.id) && selected.length >= 3"
                                @change="toggle(e.id)"
                            />
                        </td>
                        <td>
                            <Link :href="`/estimates/${e.id}/edit`" class="row-link">{{ e.title }}</Link>
                            <small>{{ e.number }}<template v-if="e.client"> · {{ e.client }}</template></small>
                        </td>
                        <td>
                            <span class="company-tag" :style="{ '--company-tone': e.company.tone, '--company-color': e.company.color }">
                                {{ e.company.short }}
                            </span>
                        </td>
                        <td>
                            <StatusPill :status="e.status" />
                            <div v-if="e.flags.aboveManagerReview || e.flags.belowTarget" class="flags">
                                <span v-if="e.flags.aboveManagerReview" class="flag">Manager review</span>
                                <span v-if="e.flags.belowTarget" class="flag">Below margin</span>
                            </div>
                        </td>
                        <td class="amount">{{ money(e.total) }}</td>
                        <td>{{ date(e.updatedAt) }}</td>
                        <td>
                            <Link :href="`/estimates/${e.id}/proposal`" class="icon-button" aria-label="Proposal">
                                <FileText :size="16" />
                            </Link>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div v-if="!estimates.data.length" class="empty">
            <span class="empty-icon"><ClipboardList :size="20" /></span>
            <h3>No estimates yet</h3>
            <p>Start with a service template or build an estimate from scratch.</p>
            <Link href="/templates" class="button secondary compact">Browse templates</Link>
        </div>

        <div class="table-foot">
            <span>{{ estimates.total }} estimates</span>
            <span v-if="estimates.last_page > 1" class="pager">
                <template v-for="link in estimates.links" :key="link.label">
                    <Link
                        v-if="link.url"
                        :href="link.url"
                        class="text-button"
                        :class="{ active: link.active }"
                        preserve-scroll
                        ><span v-html="link.label"
                    /></Link>
                </template>
            </span>
            <span v-else>USD</span>
        </div>
    </section>

</template>
