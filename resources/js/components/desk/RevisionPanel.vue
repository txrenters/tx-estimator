<script setup lang="ts">
import { History } from '@lucide/vue';
import StatusPill from '@/components/desk/StatusPill.vue';
import { date, money } from '@/lib/format';
import type { BuilderEstimate, Revision } from '@/types/desk';

defineProps<{ estimate: BuilderEstimate; revisions: Revision[]; readonly: boolean }>();
const emit = defineEmits<{ load: [revision: Revision] }>();
</script>

<template>
    <section class="panel revision-panel">
        <h3><History :size="16" /> History</h3>
        <p v-if="!revisions.length" class="muted">No saved revisions yet.</p>
        <ol v-else class="revision-list">
            <li v-for="(revision, i) in revisions" :key="revision.id" :class="{ current: i === 0 }">
                <div class="revision-top">
                    <strong>Revision {{ revisions.length - i }}</strong>
                    <span v-if="i === 0" class="revision-current">Current</span>
                    <strong class="revision-total">{{ money(revision.total) }}</strong>
                </div>
                <div class="revision-meta">
                    <span>{{ date(revision.createdAt) }} · {{ revision.user }}</span>
                    <StatusPill :status="revision.status" />
                </div>
                <button v-if="i > 0 && !readonly" type="button" class="text-button" @click="emit('load', revision)">Load this version</button>
            </li>
        </ol>
        <p v-if="revisions.length > 1 && !readonly" class="muted revision-hint">Loading a version puts it in the editor; save to keep it.</p>
    </section>
</template>
