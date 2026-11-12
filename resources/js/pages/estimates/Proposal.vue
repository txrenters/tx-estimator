<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, Printer } from '@lucide/vue';
import { ref } from 'vue';
import ProposalDocument from '@/components/desk/ProposalDocument.vue';
import type { ProposalData } from '@/types/desk';

defineOptions({ layout: { crumb: 'Proposal' } });

defineProps<{
    proposal: ProposalData & { id: number };
    authorization: { name: string; at: string } | null;
}>();

const audience = ref<'Customer' | 'Property owner'>('Property owner');

function print() {
    window.print();
}
</script>

<template>
    <Head :title="`${proposal.title || 'Service proposal'} — Proposal`" />
    <div class="proposal-wrap">
        <div class="proposal-tools">
            <Link
                :href="`/estimates/${proposal.id}/edit`"
                class="button secondary"
                ><ArrowLeft :size="16" /> Back to estimate</Link
            >
            <label>
                Audience
                <select v-model="audience">
                    <option>Customer</option>
                    <option>Property owner</option>
                </select>
            </label>
            <button type="button" class="button primary" @click="print">
                <Printer :size="16" /> Print / Save PDF
            </button>
        </div>

        <ProposalDocument
            :proposal="proposal"
            :audience="audience"
            :authorization="authorization"
        />
    </div>
</template>
