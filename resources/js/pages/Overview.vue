<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import {
    BadgeCheck,
    ChevronRight,
    ClipboardList,
    Clock3,
    Percent,
    XCircle,
} from '@lucide/vue';
import { ref } from 'vue';
import BrandIcon from '@/components/desk/BrandIcon.vue';
import StatTile from '@/components/desk/StatTile.vue';
import { date, money } from '@/lib/format';
import type { CompanySummary } from '@/types/desk';

defineOptions({ layout: { crumb: 'Overview' } });

defineProps<{
    scope: CompanySummary | null;
    tiles: {
        total: number;
        pending: number;
        declined: number;
        approvedValue: number;
        averageMargin: number | null;
    };
    companies: (CompanySummary & {
        estimates: number;
        pending: number;
        approvedValue: number;
        averageMargin: number | null;
    })[];
    pendingQueue: {
        id: number;
        number: string;
        title: string;
        company: CompanySummary;
        creator: string;
        total: number;
        flags: { aboveManagerReview: boolean; belowTarget: boolean };
        submittedAt: string;
    }[];
    activity: {
        id: number;
        action: string;
        user: string;
        estimateId: number;
        number: string;
        title: string;
        company: string;
        at: string;
    }[];
}>();

const verbs: Record<string, string> = {
    submitted: 'submitted',
    withdrawn: 'withdrew',
    approved: 'approved',
    declined: 'declined',
    reverted_to_draft: 'reopened',
    shared: 'shared a link to',
    unshared: 'turned off the link to',
    customer_authorized: 'authorized',
    authorization_cleared: 'cleared the authorization on',
};

const busy = ref<number | null>(null);
function decide(id: number, action: 'approve' | 'decline') {
    busy.value = id;
    router.post(
        `/estimates/${id}/status`,
        { action },
        { preserveScroll: true, onFinish: () => (busy.value = null) },
    );
}

function openCompany(id: number) {
    router.post(
        '/current-company',
        { company_id: id },
        { onSuccess: () => router.visit('/estimates') },
    );
}
</script>

<template>
    <Head title="Overview" />
    <div class="page-heading dashboard-heading">
        <div>
            <div class="eyebrow">
                <i />{{ scope ? scope.name.toUpperCase() : 'ALL COMPANIES' }}
            </div>
            <h1>Overview</h1>
            <p v-if="scope">
                Estimates, approvals and margin for {{ scope.name }}.
            </p>
            <p v-else>Estimates, approvals and margin across every company.</p>
        </div>
    </div>

    <div class="metrics overview-metrics">
        <StatTile
            label="Total estimates"
            :value="tiles.total"
            :hint="scope ? 'In this company' : 'Across all companies'"
            :icon="ClipboardList"
            tone="green"
        />
        <StatTile
            label="Pending approval"
            :value="tiles.pending"
            hint="Awaiting a decision"
            :icon="Clock3"
            tone="orange"
        />
        <StatTile
            label="Approved value"
            :value="money(tiles.approvedValue)"
            hint="Total approved estimates"
            :icon="BadgeCheck"
            tone="blue"
        />
        <StatTile
            label="Declined"
            :value="tiles.declined"
            hint="Declined estimates"
            :icon="XCircle"
            tone="purple"
        />
        <StatTile
            label="Average margin"
            :value="
                tiles.averageMargin === null ? '—' : `${tiles.averageMargin}%`
            "
            hint="Priced estimates"
            :icon="Percent"
            tone="green"
        />
    </div>

    <section class="panel">
        <div class="table-heading">
            <div>
                <h2>{{ scope ? 'Company' : 'Companies' }}</h2>
                <p>
                    {{
                        scope
                            ? 'Switch to All companies in the sidebar to compare every company.'
                            : 'Open a company to work in it.'
                    }}
                </p>
            </div>
        </div>
        <div class="scroll">
            <table class="estimate-table">
                <thead>
                    <tr>
                        <th>Company</th>
                        <th>Estimates</th>
                        <th>Pending</th>
                        <th>Approved value</th>
                        <th>Average margin</th>
                        <th />
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="c in companies" :key="c.id">
                        <td>
                            <span class="company-cell">
                                <BrandIcon :company="c" />
                                <button
                                    type="button"
                                    class="row-link"
                                    @click="openCompany(c.id)"
                                >
                                    {{ c.name }}
                                </button>
                            </span>
                        </td>
                        <td>{{ c.estimates }}</td>
                        <td>{{ c.pending }}</td>
                        <td class="amount">{{ money(c.approvedValue) }}</td>
                        <td>
                            {{
                                c.averageMargin === null
                                    ? '—'
                                    : `${c.averageMargin}%`
                            }}
                        </td>
                        <td>
                            <button
                                type="button"
                                class="icon-button"
                                :aria-label="`Open ${c.name}`"
                                @click="openCompany(c.id)"
                            >
                                <ChevronRight :size="16" />
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

    <div class="overview-grid">
        <section class="panel">
            <div class="table-heading">
                <div>
                    <h2>Pending approval</h2>
                    <p>Oldest first.</p>
                </div>
            </div>
            <div v-if="pendingQueue.length" class="scroll">
                <table class="estimate-table">
                    <thead>
                        <tr>
                            <th>Estimate</th>
                            <th>Company</th>
                            <th>Total</th>
                            <th />
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="e in pendingQueue" :key="e.id">
                            <td>
                                <Link
                                    :href="`/estimates/${e.id}/edit`"
                                    class="row-link"
                                    >{{ e.title }}</Link
                                >
                                <small
                                    >{{ e.number }} · {{ e.creator }} ·
                                    {{ date(e.submittedAt) }}</small
                                >
                                <div
                                    v-if="
                                        e.flags.aboveManagerReview ||
                                        e.flags.belowTarget
                                    "
                                    class="flags"
                                >
                                    <span
                                        v-if="e.flags.aboveManagerReview"
                                        class="flag"
                                        >Manager review</span
                                    >
                                    <span
                                        v-if="e.flags.belowTarget"
                                        class="flag"
                                        >Below margin</span
                                    >
                                </div>
                            </td>
                            <td>
                                <span
                                    class="company-tag"
                                    :style="{
                                        '--company-tone': e.company.tone,
                                        '--company-color': e.company.color,
                                    }"
                                >
                                    {{ e.company.short }}
                                </span>
                            </td>
                            <td class="amount">{{ money(e.total) }}</td>
                            <td>
                                <span class="row-actions">
                                    <button
                                        type="button"
                                        class="button primary compact"
                                        :disabled="busy === e.id"
                                        @click="decide(e.id, 'approve')"
                                    >
                                        Approve
                                    </button>
                                    <button
                                        type="button"
                                        class="button secondary compact"
                                        :disabled="busy === e.id"
                                        @click="decide(e.id, 'decline')"
                                    >
                                        Decline
                                    </button>
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div v-else class="empty">
                <span class="empty-icon"><BadgeCheck :size="20" /></span>
                <h3>Nothing waiting</h3>
                <p>Estimates sent for approval appear here.</p>
            </div>
        </section>

        <section class="panel">
            <div class="table-heading">
                <div>
                    <h2>Recent activity</h2>
                    <p>Submissions and decisions.</p>
                </div>
            </div>
            <ul v-if="activity.length" class="activity-list">
                <li v-for="a in activity" :key="a.id">
                    <strong>{{ a.user }}</strong>
                    {{ verbs[a.action] ?? a.action }}
                    <Link
                        :href="`/estimates/${a.estimateId}/edit`"
                        class="row-link"
                        >{{ a.title }}</Link
                    >
                    <small
                        >{{ a.number }} · {{ a.company }} ·
                        {{ date(a.at) }}</small
                    >
                </li>
            </ul>
            <div v-else class="empty">
                <h3>No activity yet</h3>
                <p>Approvals and submissions show up here.</p>
            </div>
        </section>
    </div>
</template>
