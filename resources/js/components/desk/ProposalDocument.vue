<script setup lang="ts">
import BrandIcon from '@/components/desk/BrandIcon.vue';
import { date, money } from '@/lib/format';
import type { ProposalData } from '@/types/desk';

defineProps<{
    proposal: ProposalData;
    audience: string;
    authorization: { name: string; at: string } | null;
}>();
</script>

<template>
    <article class="proposal">
        <div class="proposal-top">
            <img
                v-if="proposal.company.logoUrl"
                :src="proposal.company.logoUrl"
                :alt="`${proposal.company.name} logo`"
                class="company-logo logo"
            />
            <BrandIcon v-else :company="proposal.company" />
            <div>
                <strong>{{ proposal.company.name }}</strong>
                <p>{{ proposal.company.location }}</p>
            </div>
        </div>

        <div class="proposal-title">
            <div>
                <span class="eyebrow">{{ audience }} proposal</span>
                <h1>{{ proposal.title || 'Service proposal' }}</h1>
            </div>
            <div>{{ proposal.number }}<br />{{ date(proposal.date) }}</div>
        </div>

        <div class="proposal-client">
            <strong>{{ proposal.client || 'Prepared for' }}</strong
            ><br />
            {{ proposal.address || 'Service address' }}<br />
            {{ proposal.service }}
        </div>

        <section v-if="proposal.scope">
            <h3>Scope of work</h3>
            <p class="multiline">{{ proposal.scope }}</p>
        </section>

        <section>
            <h3>Proposed pricing</h3>
            <table>
                <thead>
                    <tr>
                        <th>Service item</th>
                        <th>Quantity</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(line, i) in proposal.lines" :key="i">
                        <td>{{ line.name }}</td>
                        <td>{{ line.qty }} {{ line.unit }}</td>
                        <td>{{ money(line.amount) }}</td>
                    </tr>
                    <tr v-if="proposal.discount > 0">
                        <td>Discount</td>
                        <td />
                        <td>−{{ money(proposal.discount) }}</td>
                    </tr>
                    <tr v-if="proposal.trip > 0">
                        <td>Trip charge</td>
                        <td />
                        <td>{{ money(proposal.trip) }}</td>
                    </tr>
                </tbody>
            </table>
            <div class="total-row">
                <span>Proposed total</span>
                <strong>{{ money(proposal.total) }}</strong>
            </div>
        </section>

        <section v-if="proposal.terms">
            <h3>Terms and exclusions</h3>
            <p class="multiline">{{ proposal.terms }}</p>
        </section>

        <div v-if="authorization" class="signature signed">
            <span>
                <strong class="signed-name">{{ authorization.name }}</strong>
                {{ audience }} authorization
            </span>
            <span>
                <strong class="signed-name">{{
                    date(authorization.at)
                }}</strong>
                Date
            </span>
        </div>
        <slot v-else name="authorize">
            <div class="signature">
                <span>{{ audience }} authorization</span>
                <span>Date</span>
            </div>
        </slot>
    </article>
</template>
