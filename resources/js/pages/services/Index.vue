<script setup lang="ts">
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import { Archive, ArchiveRestore, Check, Pencil, Plus, Trash2, X } from '@lucide/vue';
import { computed, ref } from 'vue';
import { Dialog, DialogContent, DialogDescription, DialogTitle } from '@/components/ui/dialog';
import { serviceIcon } from '@/lib/serviceIcons';
import type { DeskProps } from '@/types/desk';

defineOptions({ layout: { crumb: 'Services' } });

interface Service {
    id: number;
    name: string;
    isActive: boolean;
    usage: number;
    offeredBy: string[];
}
interface Group {
    id: number;
    name: string;
    icon: string;
    color: string;
    services: Service[];
}

const props = defineProps<{ groups: Group[]; icons: string[] }>();

const errors = computed(() => usePage().props.errors as Record<string, string>);
const scope = computed(() => (usePage().props.desk as DeskProps).currentCompany);

// With a company selected, show its trades only unless "Show all trades" is on.
const showAllTrades = ref(false);
const visibleGroups = computed(() => {
    if (!scope.value || showAllTrades.value) {
        return props.groups;
    }
    const short = scope.value.short;

    return props.groups.filter((g) => g.services.some((s) => s.offeredBy.includes(short)));
});

// Trade dialog (add or edit).
const tradeOpen = ref(false);
const editingTrade = ref<Group | null>(null);
const trade = useForm({ name: '', icon: 'wrench', color: '#17664e' });

function openTrade(group: Group | null) {
    editingTrade.value = group;
    trade.clearErrors();
    trade.name = group?.name ?? '';
    trade.icon = group?.icon ?? 'wrench';
    trade.color = group?.color ?? '#17664e';
    tradeOpen.value = true;
}

function saveTrade() {
    const options = { preserveScroll: true, onSuccess: () => (tradeOpen.value = false) };
    if (editingTrade.value) {
        trade.put(`/service-groups/${editingTrade.value.id}`, options);
    } else {
        trade.post('/service-groups', options);
    }
}

// Inline "add service" per trade.
const newService = ref<Record<number, string>>({});
function addService(group: Group) {
    const name = (newService.value[group.id] ?? '').trim();
    if (!name) return;
    router.post('/services', { name, service_group_id: group.id }, {
        preserveScroll: true,
        onSuccess: () => (newService.value[group.id] = ''),
    });
}

// Inline rename.
const renaming = ref<number | null>(null);
const renameText = ref('');
function startRename(service: Service) {
    renaming.value = service.id;
    renameText.value = service.name;
}
function saveService(service: Service, group: Group, changes: Partial<{ name: string; is_active: boolean }>) {
    router.put(`/services/${service.id}`, {
        name: changes.name ?? service.name,
        service_group_id: group.id,
        is_active: changes.is_active ?? service.isActive,
    }, { preserveScroll: true, onSuccess: () => (renaming.value = null) });
}
function removeService(service: Service) {
    router.delete(`/services/${service.id}`, { preserveScroll: true });
}
</script>

<template>
    <Head title="Services" />
    <div class="page-heading">
        <div>
            <span class="eyebrow">SERVICE CATALOG</span>
            <h1>Services</h1>
            <p>Trades and services companies can offer. Renaming carries through to existing estimates and templates.</p>
        </div>
        <div class="actions">
            <button type="button" class="button primary" @click="openTrade(null)"><Plus :size="16" /> New trade</button>
        </div>
    </div>

    <p v-if="scope" class="notice">
        <span>
            Showing {{ showAllTrades ? 'every trade' : `${scope.name}'s trades` }}. To choose what it offers, use
            <Link href="/company/settings" class="text-button">Company settings</Link>.
        </span>
        <button type="button" class="button secondary compact" style="margin-left: auto" @click="showAllTrades = !showAllTrades">
            {{ showAllTrades ? `Only ${scope.short}` : 'Show all trades' }}
        </button>
    </p>
    <p v-if="errors.service" class="notice bad">{{ errors.service }}</p>
    <p v-if="errors.name && !tradeOpen" class="notice bad">{{ errors.name }}</p>

    <div class="trade-grid">
        <section v-for="group in visibleGroups" :key="group.id" class="panel section trade-card">
            <header class="trade-head">
                <span class="brand-icon" :style="{ '--company-color': group.color, '--company-tone': `color-mix(in srgb, ${group.color} 12%, white)` }">
                    <component :is="serviceIcon(group.icon)" :size="18" />
                </span>
                <h3>{{ group.name }}</h3>
                <button type="button" class="icon-button" :aria-label="`Edit ${group.name}`" @click="openTrade(group)"><Pencil :size="15" /></button>
            </header>

            <ul class="service-list">
                <li v-for="service in group.services" :key="service.id" :class="{ retired: !service.isActive }">
                    <template v-if="renaming === service.id">
                        <input v-model="renameText" :aria-label="`Rename ${service.name}`" @keydown.enter="saveService(service, group, { name: renameText })" @keydown.esc="renaming = null" />
                        <button type="button" class="icon-button" aria-label="Save name" @click="saveService(service, group, { name: renameText })"><Check :size="15" /></button>
                        <button type="button" class="icon-button" aria-label="Cancel" @click="renaming = null"><X :size="15" /></button>
                    </template>
                    <template v-else>
                        <span class="service-name">
                            <span>
                                {{ service.name }}
                                <small v-if="!service.isActive">Retired</small>
                                <small v-else-if="service.usage">Used {{ service.usage }}×</small>
                                <span class="offered-by">
                                    <template v-if="service.offeredBy.length">
                                        <span
                                            v-for="c in service.offeredBy"
                                            :key="c"
                                            class="offered-chip"
                                            :class="{ current: scope && c === scope.short }"
                                            >{{ c }}</span
                                        >
                                    </template>
                                    <em v-else>Not offered by any company</em>
                                </span>
                            </span>
                        </span>
                        <button type="button" class="icon-button" :aria-label="`Rename ${service.name}`" @click="startRename(service)"><Pencil :size="14" /></button>
                        <button
                            type="button"
                            class="icon-button"
                            :aria-label="service.isActive ? `Retire ${service.name}` : `Restore ${service.name}`"
                            :title="service.isActive ? 'Retire: hide from new estimates' : 'Restore'"
                            @click="saveService(service, group, { is_active: !service.isActive })"
                        >
                            <Archive v-if="service.isActive" :size="14" />
                            <ArchiveRestore v-else :size="14" />
                        </button>
                        <button
                            v-if="!service.usage"
                            type="button"
                            class="icon-button"
                            :aria-label="`Delete ${service.name}`"
                            title="Delete (only when unused)"
                            @click="removeService(service)"
                        >
                            <Trash2 :size="14" />
                        </button>
                    </template>
                </li>
            </ul>

            <form class="add-service" @submit.prevent="addService(group)">
                <input v-model="newService[group.id]" :aria-label="`New service in ${group.name}`" placeholder="Add a service…" />
                <button type="submit" class="button secondary compact"><Plus :size="14" /> Add</button>
            </form>
        </section>
    </div>

    <Dialog v-model:open="tradeOpen">
        <DialogContent class="desk-dialog sm:max-w-md">
            <div class="desk">
                <DialogTitle as="h2">{{ editingTrade ? 'Edit trade' : 'New trade' }}</DialogTitle>
                <DialogDescription class="muted">A trade groups related services, like HVAC or Electrical.</DialogDescription>
                <form class="fields" @submit.prevent="saveTrade">
                    <label class="wide">
                        Name
                        <input v-model="trade.name" placeholder="e.g. Electrical" autofocus />
                        <span v-if="trade.errors.name" class="form-error">{{ trade.errors.name }}</span>
                    </label>
                    <div class="wide">
                        <span class="status-label">Icon</span>
                        <div class="icon-picker">
                            <button
                                v-for="icon in props.icons"
                                :key="icon"
                                type="button"
                                :class="{ selected: trade.icon === icon }"
                                :aria-label="icon"
                                @click="trade.icon = icon"
                            >
                                <component :is="serviceIcon(icon)" :size="18" />
                            </button>
                        </div>
                    </div>
                    <label>
                        Colour
                        <div class="unit-input">
                            <input v-model="trade.color" type="color" aria-label="Trade colour" class="color-input" />
                            <input v-model="trade.color" aria-label="Trade colour hex" />
                        </div>
                        <span v-if="trade.errors.color" class="form-error">{{ trade.errors.color }}</span>
                    </label>
                    <div class="actions wide dialog-actions">
                        <button type="button" class="button secondary" @click="tradeOpen = false">Cancel</button>
                        <button type="submit" class="button primary" :disabled="trade.processing"><Check :size="16" /> Save trade</button>
                    </div>
                </form>
            </div>
        </DialogContent>
    </Dialog>
</template>
