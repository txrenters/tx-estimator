<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { PenLine, Printer } from '@lucide/vue';
import ProposalDocument from '@/components/desk/ProposalDocument.vue';
import { money } from '@/lib/format';
import type { ProposalData } from '@/types/desk';

const props = defineProps<{
    proposal: ProposalData;
    authorization: { name: string; at: string } | null;
    authorizeUrl: string;
}>();

const form = useForm({ name: '', agree: false });

function sign() {
    form.post(props.authorizeUrl, { preserveScroll: true });
}

function print() {
    window.print();
}
</script>

<template>
    <Head :title="`${proposal.title || 'Service proposal'} — ${proposal.company.name}`">
        <meta name="robots" content="noindex, nofollow" />
    </Head>
    <div class="desk public-proposal">
        <div class="proposal-wrap">
            <div class="proposal-tools">
                <span class="public-from">Proposal from <strong>{{ proposal.company.name }}</strong></span>
                <button type="button" class="button secondary" @click="print"><Printer :size="16" /> Print / Save PDF</button>
            </div>

            <ProposalDocument :proposal="proposal" audience="Property owner" :authorization="authorization">
                <template #authorize>
                    <form class="authorize-box" @submit.prevent="sign">
                        <h3><PenLine :size="17" /> Property owner authorization</h3>
                        <p class="muted">
                            To approve this proposal for {{ money(proposal.total) }}, type your full name below. It will appear as your
                            signature on this proposal.
                        </p>
                        <div class="authorize-row">
                            <label>
                                Full name
                                <input v-model="form.name" autocomplete="name" placeholder="e.g. Jane Roe" maxlength="120" />
                            </label>
                            <div class="signature-preview" aria-hidden="true">{{ form.name || ' ' }}</div>
                        </div>
                        <label class="agree">
                            <input v-model="form.agree" type="checkbox" />
                            I authorize {{ proposal.company.name }} to perform the work in this proposal.
                        </label>
                        <span v-if="form.errors.name" class="form-error">{{ form.errors.name }}</span>
                        <span v-if="form.errors.agree" class="form-error">{{ form.errors.agree }}</span>
                        <button type="submit" class="button primary" :disabled="form.processing || !form.name.trim() || !form.agree">
                            <PenLine :size="16" /> Authorize proposal
                        </button>
                    </form>
                </template>
            </ProposalDocument>

            <p v-if="authorization" class="notice authorized-note">
                Thank you, {{ authorization.name }}. Your authorization has been sent to {{ proposal.company.name }}.
            </p>
        </div>
    </div>
</template>
