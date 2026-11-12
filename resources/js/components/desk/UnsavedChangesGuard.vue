<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import type { PendingVisit } from '@inertiajs/core';
import { Check, Save, Trash2 } from '@lucide/vue';
import { onBeforeUnmount, onMounted, ref } from 'vue';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogTitle,
} from '@/components/ui/dialog';

/**
 * Asks before leaving a page with unsaved changes: Save changes / Discard / Keep editing.
 * Intercepts in-app navigation (links, sidebar, company switcher, duplicate); a full reload
 * or closing the tab gets the browser's own "Leave site?" prompt.
 */
const props = defineProps<{
    dirty: boolean;
    /** Save the page; call onSuccess when saved, onError if validation failed. */
    save: (callbacks: { onSuccess: () => void; onError: () => void }) => void;
}>();

const open = ref(false);
const saving = ref(false);
let pending: PendingVisit | null = null;
let leaving = false;

// Requests that move away from (or reload over) this page. The page's own saves and
// in-place actions (share link, status buttons) aren't intercepted.
function leavesPage(visit: PendingVisit): boolean {
    if (visit.prefetch || visit.only.length > 0) {
        return false;
    }

    if (visit.method === 'get') {
        return true;
    }

    const path = visit.url.pathname;

    return path === '/current-company' || path.endsWith('/duplicate');
}

function onBeforeVisit(event: CustomEvent<{ visit: PendingVisit }>) {
    const visit = event.detail.visit;

    if (leaving || !props.dirty || !leavesPage(visit)) {
        return;
    }

    event.preventDefault();
    pending = visit;
    open.value = true;
}

function proceed() {
    open.value = false;
    const visit = pending;
    pending = null;

    if (!visit) {
        return;
    }

    leaving = true;
    router.visit(visit.url.href, {
        method: visit.method,
        data: visit.method === 'get' ? {} : visit.data,
        preserveScroll: visit.preserveScroll,
        replace: visit.replace,
        onFinish: () => (leaving = false),
    });
}

function saveAndLeave() {
    saving.value = true;
    props.save({
        onSuccess: () => {
            saving.value = false;
            proceed();
        },
        onError: () => {
            // Validation failed: stay here so the highlighted fields can be fixed.
            saving.value = false;
            open.value = false;
            pending = null;
        },
    });
}

function keepEditing() {
    open.value = false;
    pending = null;
}

function onBeforeUnload(event: BeforeUnloadEvent) {
    if (props.dirty && !leaving) {
        event.preventDefault();
        event.returnValue = '';
    }
}

let removeListener: (() => void) | null = null;

onMounted(() => {
    removeListener = router.on(
        'before',
        onBeforeVisit as (event: CustomEvent) => void,
    );
    window.addEventListener('beforeunload', onBeforeUnload);
});

onBeforeUnmount(() => {
    removeListener?.();
    window.removeEventListener('beforeunload', onBeforeUnload);
});
</script>

<template>
    <Dialog
        :open="open"
        @update:open="(value: boolean) => !value && keepEditing()"
    >
        <DialogContent
            class="desk-dialog sm:max-w-md"
            data-test="unsaved-dialog"
        >
            <div class="desk">
                <DialogTitle as="h2">You have unsaved changes</DialogTitle>
                <DialogDescription class="muted"
                    >Save them before leaving, or discard
                    them.</DialogDescription
                >
                <div class="actions dialog-actions unsaved-actions">
                    <button
                        type="button"
                        class="button secondary"
                        @click="keepEditing"
                    >
                        Keep editing
                    </button>
                    <span class="actions">
                        <button
                            type="button"
                            class="button danger-outline"
                            :disabled="saving"
                            @click="proceed"
                        >
                            <Trash2 :size="15" /> Discard
                        </button>
                        <button
                            type="button"
                            class="button primary"
                            :disabled="saving"
                            @click="saveAndLeave"
                        >
                            <Save v-if="!saving" :size="15" /><Check
                                v-else
                                :size="15"
                            />
                            {{ saving ? 'Saving…' : 'Save changes' }}
                        </button>
                    </span>
                </div>
            </div>
        </DialogContent>
    </Dialog>
</template>
