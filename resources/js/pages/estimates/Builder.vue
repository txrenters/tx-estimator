<script setup lang="ts">
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import { ArrowLeft, BadgeCheck, Check, Copy, ExternalLink, FileText, Link2, Send, Trash2, Undo2, X } from '@lucide/vue';
import { toast } from 'vue-sonner';
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { CALCULATOR_LINE_EVENT } from '@/composables/useCalculator';
import LineItemsEditor from '@/components/desk/LineItemsEditor.vue';
import PricingFields from '@/components/desk/PricingFields.vue';
import PricingSummary from '@/components/desk/PricingSummary.vue';
import RevisionPanel from '@/components/desk/RevisionPanel.vue';
import UnsavedChangesGuard from '@/components/desk/UnsavedChangesGuard.vue';
import StatusPill from '@/components/desk/StatusPill.vue';
import type { BuilderEstimate, DeskProps, Pricing, Revision, ShareLink } from '@/types/desk';
import { date as formatDate } from '@/lib/format';
import { Dialog, DialogContent, DialogDescription, DialogTitle } from '@/components/ui/dialog';

defineOptions({ layout: { crumb: 'Estimate builder' } });

const props = defineProps<{
    estimate: BuilderEstimate;
    can: Record<'update' | 'delete' | 'duplicate' | 'submit' | 'withdraw' | 'approve' | 'decline' | 'share', boolean>;
    share: ShareLink | null;
    companyDefaults: Record<number, Pricing>;
    revisions: Revision[];
}>();

const desk = computed(() => usePage().props.desk as DeskProps);
const readonly = computed(() => !props.can.update);
const isNew = computed(() => props.estimate.id === null);
const canPickCompany = computed(() => isNew.value && desk.value.can.switchCompany);

const form = useForm({
    title: props.estimate.title,
    company_id: props.estimate.company_id ?? (canPickCompany.value ? null : desk.value.currentCompany?.id ?? null),
    service_type: props.estimate.service_type,
    client: props.estimate.client,
    address: props.estimate.address,
    lines: props.estimate.lines.map((l) => ({ ...l })),
    pricing: { ...props.estimate.pricing },
    scope: props.estimate.scope,
    terms: props.estimate.terms,
    notes: props.estimate.notes,
});

watch(
    () => form.company_id,
    (id, old) => {
        if (isNew.value && id && id !== old && props.companyDefaults[id]) {
            form.pricing = { ...props.companyDefaults[id] };
        }
    },
);

const selectedCompany = computed(() =>
    desk.value.companies.find((c) => c.id === (form.company_id ?? props.estimate.company_id)),
);
const serviceOptions = computed(() => selectedCompany.value?.services ?? desk.value.serviceTypes);

watch(serviceOptions, (options) => {
    if (!readonly.value && !options.includes(form.service_type)) {
        form.service_type = options[0] ?? form.service_type;
    }
}, { immediate: true });

const companyName = computed(
    () => desk.value.companies.find((c) => c.id === (form.company_id ?? props.estimate.company_id))?.name ?? '',
);

function save({ onSuccess, onError }: { onSuccess?: () => void; onError?: () => void } = {}) {
    const options = { preserveScroll: true, onSuccess: () => onSuccess?.(), onError: () => onError?.() };
    if (isNew.value) {
        form.post('/estimates', options);
    } else {
        form.put(`/estimates/${props.estimate.id}`, options);
    }
}

const busy = ref(false);
function transition(action: 'submit' | 'withdraw' | 'approve' | 'decline') {
    busy.value = true;
    router.post(`/estimates/${props.estimate.id}/status`, { action }, {
        preserveScroll: true,
        onFinish: () => (busy.value = false),
    });
}

function duplicate() {
    router.post(`/estimates/${props.estimate.id}/duplicate`);
}

const confirmingDelete = ref(false);
function destroy() {
    router.delete(`/estimates/${props.estimate.id}`);
}

const restoredNotice = ref('');
function loadRevision(revision: Revision) {
    const s = revision.snapshot;
    form.title = s.title ?? form.title;
    form.service_type = s.service_type ?? form.service_type;
    form.client = s.client ?? '';
    form.address = s.address ?? '';
    form.lines = (s.lines ?? form.lines).map((l) => ({ ...l }));
    form.pricing = { ...(s.pricing ?? form.pricing) };
    form.scope = s.scope ?? '';
    form.terms = s.terms ?? '';
    form.notes = s.notes ?? '';
    restoredNotice.value = 'Revision loaded into the editor. Save to keep it.';
    window.scrollTo(0, 0);
}

const errors = computed(() => form.errors as Record<string, string>);

// Customer share link.
const shareOpen = ref(false);
const shareExpiry = ref<number | null>(null);
const copied = ref(false);
function createShare() {
    router.post(`/estimates/${props.estimate.id}/share`, { expires_in_days: shareExpiry.value }, { preserveScroll: true });
}
function turnOffShare() {
    router.delete(`/estimates/${props.estimate.id}/share`, { preserveScroll: true });
}
async function copyShare() {
    if (!props.share) return;
    try {
        await navigator.clipboard.writeText(props.share.url);
    } catch {
        (document.getElementById('share-url') as HTMLInputElement | null)?.select();
        document.execCommand('copy');
    }
    copied.value = true;
    setTimeout(() => (copied.value = false), 2000);
}

// The floating calculator can drop its result in as a line: adjusted qty × unit cost (as materials).
function addCalculatorLine(event: Event) {
    if (readonly.value) {
        toast.error('This estimate is read-only.');
        return;
    }

    const { qty, materials } = (event as CustomEvent<{ qty: number; materials: number }>).detail;
    const line = { name: '', qty, unit: 'job', labor: 0, materials, fixed: null };
    const last = form.lines[form.lines.length - 1];
    const lastIsBlank = last && !last.name && !last.labor && !last.materials && last.fixed === null;

    form.lines = lastIsBlank ? [...form.lines.slice(0, -1), line] : [...form.lines, line];
    toast.success('Line added. Give it a name before saving.');
}

onMounted(() => window.addEventListener(CALCULATOR_LINE_EVENT, addCalculatorLine));
onBeforeUnmount(() => window.removeEventListener(CALCULATOR_LINE_EVENT, addCalculatorLine));
</script>

<template>
    <Head :title="estimate.title || 'New estimate'" />
    <Link href="/estimates" class="back"><ArrowLeft :size="16" /> Back to estimates</Link>

    <div class="page-heading">
        <div>
            <div class="eyebrow">{{ estimate.number ?? 'NEW' }}</div>
            <h1>{{ isNew ? 'New estimate' : form.title || 'Untitled estimate' }}</h1>
            <p>Build the scope, check pricing, and prepare a proposal.</p>
        </div>
        <div class="actions">
            <button v-if="can.share" type="button" class="button secondary" data-test="share-button" @click="shareOpen = true">
                <Link2 :size="16" /> {{ share ? 'Shared' : 'Share' }}
            </button>
            <button v-if="can.duplicate" type="button" class="button secondary" @click="duplicate">
                <Copy :size="16" /> Duplicate
            </button>
            <Link v-if="!isNew" :href="`/estimates/${estimate.id}/proposal`" class="button secondary">
                <FileText :size="16" /> Proposal
            </Link>
            <button v-else type="button" class="button secondary" disabled><FileText :size="16" /> Proposal</button>
            <button v-if="can.update" type="button" class="button primary" :disabled="form.processing" @click="save()">
                <Check :size="17" /> {{ form.processing ? 'Saving…' : 'Save estimate' }}
            </button>
        </div>
    </div>

    <p v-if="estimate.authorization" class="notice authorized-banner">
        <BadgeCheck :size="16" />
        Authorized by the property owner: <strong>{{ estimate.authorization.name }}</strong> on {{ formatDate(estimate.authorization.at) }}.
        Changing prices, line items, scope or terms will clear it; internal notes won't.
    </p>
    <p v-if="restoredNotice" class="notice">
        {{ restoredNotice }}
        <button type="button" class="icon-button" aria-label="Dismiss" @click="restoredNotice = ''"><X :size="15" /></button>
    </p>
    <p v-if="form.hasErrors" class="notice bad">Check the highlighted fields before saving.</p>
    <p v-if="readonly" class="notice">
        You can view this estimate but not change it.
    </p>

    <div class="editor-grid">
        <div>
            <section class="panel section">
                <h3>Estimate details</h3>
                <div class="fields">
                    <label>
                        Estimate title
                        <input v-model="form.title" placeholder="e.g. Evaporator coil replacement" :disabled="readonly" />
                        <span v-if="errors.title" class="form-error">{{ errors.title }}</span>
                    </label>
                    <div class="status-field">
                        <span class="status-label">Approval status</span>
                        <div class="status-row">
                            <StatusPill :status="estimate.status" />
                            <button v-if="can.submit" type="button" class="button secondary compact" :disabled="busy || form.isDirty" @click="transition('submit')">
                                <Send :size="14" /> Submit for approval
                            </button>
                            <button v-if="can.approve" type="button" class="button primary compact" :disabled="busy || form.isDirty" @click="transition('approve')">
                                <Check :size="14" /> Approve
                            </button>
                            <button v-if="can.decline" type="button" class="button secondary compact" :disabled="busy || form.isDirty" @click="transition('decline')">
                                <X :size="14" /> Decline
                            </button>
                            <button v-if="can.withdraw" type="button" class="button secondary compact" :disabled="busy || form.isDirty" @click="transition('withdraw')">
                                <Undo2 :size="14" /> Withdraw
                            </button>
                        </div>
                        <small v-if="form.isDirty && (can.submit || can.approve || can.decline || can.withdraw)" class="readonly-note">
                            Save your changes first.
                        </small>
                        <small v-if="estimate.approver" class="readonly-note">
                            {{ estimate.status === 'approved' ? 'Approved' : 'Reviewed' }} by {{ estimate.approver }}
                        </small>
                    </div>
                    <label>
                        Company
                        <select v-if="canPickCompany" v-model="form.company_id">
                            <option :value="null" disabled>Choose a company</option>
                            <option v-for="c in desk.companies" :key="c.id" :value="c.id">{{ c.name }}</option>
                        </select>
                        <select v-else disabled><option>{{ companyName }}</option></select>
                        <span v-if="errors.company_id" class="form-error">{{ errors.company_id }}</span>
                    </label>
                    <label>
                        Service type
                        <select v-model="form.service_type" :disabled="readonly">
                            <option v-for="s in serviceOptions" :key="s">{{ s }}</option>
                            <option v-if="!serviceOptions.includes(form.service_type)">{{ form.service_type }}</option>
                        </select>
                        <span v-if="errors.service_type" class="form-error">{{ errors.service_type }}</span>
                    </label>
                    <label>
                        Customer or owner
                        <input v-model="form.client" :disabled="readonly" />
                    </label>
                    <label>
                        Service address
                        <input v-model="form.address" placeholder="Street, city, ZIP" :disabled="readonly" />
                    </label>
                </div>
            </section>

            <section class="panel section">
                <h3>Line items</h3>
                <p class="muted">Markup applies to unit costs. A fixed sell price overrides markup for that line.</p>
                <LineItemsEditor v-model="form.lines" :markup="form.pricing.markup" :readonly="readonly" :errors="errors" />
                <span v-if="errors.lines" class="form-error">{{ errors.lines }}</span>
            </section>

            <section class="panel section">
                <h3>Pricing adjustments</h3>
                <PricingFields v-model="form.pricing" :readonly="readonly" :errors="errors" prefix="pricing." />
            </section>

            <section class="panel section">
                <h3>Scope, terms and notes</h3>
                <div class="fields one">
                    <label>Proposal scope <textarea v-model="form.scope" :disabled="readonly" /></label>
                    <label>Proposal terms and exclusions <textarea v-model="form.terms" :disabled="readonly" /></label>
                    <label>
                        Internal notes
                        <textarea v-model="form.notes" placeholder="Approval details, job conditions, vendor quotes…" :disabled="readonly" />
                    </label>
                </div>
            </section>

            <section v-if="can.delete" class="panel section">
                <h3>Delete estimate</h3>
                <p class="muted">This removes the estimate and its history.</p>
                <button v-if="!confirmingDelete" type="button" class="button danger-outline compact" @click="confirmingDelete = true">
                    <Trash2 :size="14" /> Delete estimate
                </button>
                <div v-else class="actions" style="justify-content: flex-start">
                    <button type="button" class="button danger compact" @click="destroy"><Trash2 :size="14" /> Yes, delete</button>
                    <button type="button" class="button secondary compact" @click="confirmingDelete = false">Cancel</button>
                </div>
            </section>
        </div>

        <div class="editor-side">
            <PricingSummary :lines="form.lines" :pricing="form.pricing" />
            <RevisionPanel v-if="!isNew" :estimate="estimate" :revisions="revisions" :readonly="readonly" @load="loadRevision" />
        </div>
    </div>

    <Dialog v-model:open="shareOpen">
        <DialogContent class="desk-dialog sm:max-w-lg">
            <div class="desk">
                <DialogTitle as="h2">Share with your customer</DialogTitle>
                <DialogDescription class="muted">
                    They can open the proposal without logging in and authorize it by typing their name. Your costs, margin and internal notes are never shown.
                </DialogDescription>

                <template v-if="share">
                    <div class="share-url">
                        <input id="share-url" :value="share.url" readonly aria-label="Share link" @focus="($event.target as HTMLInputElement).select()" />
                        <button type="button" class="button primary compact" @click="copyShare">
                            <Check v-if="copied" :size="15" /><Copy v-else :size="15" /> {{ copied ? 'Copied' : 'Copy' }}
                        </button>
                    </div>
                    <ul class="share-facts">
                        <li>{{ share.expiresAt ? `Expires ${formatDate(share.expiresAt)}` : 'Never expires' }}</li>
                        <li v-if="share.views">Opened {{ share.views }}× · last {{ formatDate(share.viewedAt) }}</li>
                        <li v-else>Not opened yet</li>
                        <li v-if="estimate.authorization">Authorized by {{ estimate.authorization.name }}</li>
                    </ul>
                    <div class="actions dialog-actions" style="justify-content: space-between">
                        <button type="button" class="button secondary compact" @click="turnOffShare"><X :size="14" /> Turn off link</button>
                        <span class="actions">
                            <a :href="share.url" target="_blank" rel="noopener" class="button secondary compact"><ExternalLink :size="14" /> Open</a>
                            <button type="button" class="button secondary compact" title="Makes a new link; the old one stops working" @click="createShare">New link</button>
                        </span>
                    </div>
                </template>

                <template v-else>
                    <label class="share-expiry">
                        Link expires
                        <select v-model="shareExpiry">
                            <option :value="null">Never (until you turn it off)</option>
                            <option :value="7">After 7 days</option>
                            <option :value="30">After 30 days</option>
                            <option :value="90">After 90 days</option>
                        </select>
                    </label>
                    <div class="actions dialog-actions">
                        <button type="button" class="button secondary" @click="shareOpen = false">Cancel</button>
                        <button type="button" class="button primary" data-test="create-share" @click="createShare"><Link2 :size="16" /> Create link</button>
                    </div>
                </template>
            </div>
        </DialogContent>
    </Dialog>

    <UnsavedChangesGuard :dirty="form.isDirty && !readonly" :save="save" />
</template>
