<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { Plus } from '@lucide/vue';
import { ref } from 'vue';
import BrandIcon from '@/components/desk/BrandIcon.vue';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogTitle,
} from '@/components/ui/dialog';
import type { CompanySummary } from '@/types/desk';

defineOptions({ layout: { crumb: 'Companies' } });

defineProps<{ companies: (CompanySummary & { users: number; estimates: number })[] }>();

const form = useForm<{ name: string; short_name: string; location: string; color: string; copy_from: number | null; copy: string[] }>({
    name: '',
    short_name: '',
    location: 'Greater Houston, TX',
    color: '#17664e',
    copy_from: null,
    copy: ['services', 'pricing', 'templates'],
});

const copyOptions = [
    { value: 'services', label: 'Services it offers' },
    { value: 'pricing', label: 'Pricing defaults and proposal text' },
    { value: 'templates', label: 'Service templates' },
];

const creating = ref(false);

function openCreate() {
    form.reset();
    form.clearErrors();
    creating.value = true;
}

function create() {
    form.post('/companies', {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            creating.value = false;
        },
    });
}

function open(id: number, path: string) {
    router.post('/current-company', { company_id: id }, { onSuccess: () => router.visit(path) });
}
</script>

<template>
    <Head title="Companies" />
    <div class="page-heading">
        <div>
            <span class="eyebrow">ORGANIZATIONS</span>
            <h1>Companies</h1>
            <p>Each company keeps its own estimates, templates, pricing and team.</p>
        </div>
        <div class="actions">
            <button type="button" class="button primary" @click="openCreate"><Plus :size="16" /> New company</button>
        </div>
    </div>

    <section class="panel">
        <div class="scroll">
            <table class="estimate-table">
                <thead>
                    <tr>
                        <th>Company</th>
                        <th>Location</th>
                        <th>Users</th>
                        <th>Estimates</th>
                        <th />
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="c in companies" :key="c.id">
                        <td>
                            <span class="company-cell">
                                <BrandIcon :company="c" />
                                <button type="button" class="row-link" @click="open(c.id, '/estimates')">{{ c.name }}</button>
                            </span>
                        </td>
                        <td>{{ c.location }}</td>
                        <td>{{ c.users }}</td>
                        <td>{{ c.estimates }}</td>
                        <td>
                            <button type="button" class="text-button" @click="open(c.id, '/company/settings')">Settings</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

    <Dialog v-model:open="creating">
        <DialogContent class="desk-dialog sm:max-w-lg">
            <div class="desk">
                <DialogTitle as="h2">New company</DialogTitle>
                <DialogDescription class="muted">
                    Start blank or copy the setup of an existing company. Add a logo afterwards in its settings.
                </DialogDescription>
                <form class="fields" @submit.prevent="create">
                    <label class="wide">
                        Company name
                        <input v-model="form.name" autofocus />
                        <span v-if="form.errors.name" class="form-error">{{ form.errors.name }}</span>
                    </label>
                    <label>
                        Short name
                        <input v-model="form.short_name" placeholder="e.g. Plumbing" />
                        <span v-if="form.errors.short_name" class="form-error">{{ form.errors.short_name }}</span>
                    </label>
                    <label>
                        Brand color
                        <div class="unit-input">
                            <input v-model="form.color" type="color" aria-label="Brand color" class="color-input" />
                            <input v-model="form.color" aria-label="Brand color hex" />
                        </div>
                        <span v-if="form.errors.color" class="form-error">{{ form.errors.color }}</span>
                    </label>
                    <label class="wide">
                        Start from
                        <select v-model="form.copy_from">
                            <option :value="null">Blank company (all services, default pricing)</option>
                            <option v-for="c in companies" :key="c.id" :value="c.id">Copy from {{ c.name }}</option>
                        </select>
                        <span v-if="form.errors.copy_from" class="form-error">{{ form.errors.copy_from }}</span>
                    </label>
                    <div v-if="form.copy_from" class="wide pick-list">
                        <label v-for="o in copyOptions" :key="o.value">
                            <input v-model="form.copy" type="checkbox" :value="o.value" />
                            {{ o.label }}
                        </label>
                    </div>
                    <label class="wide">
                        Location
                        <input v-model="form.location" />
                        <span v-if="form.errors.location" class="form-error">{{ form.errors.location }}</span>
                    </label>
                    <div class="actions wide dialog-actions">
                        <button type="button" class="button secondary" @click="creating = false">Cancel</button>
                        <button type="submit" class="button primary" :disabled="form.processing">
                            <Plus :size="16" /> Create company
                        </button>
                    </div>
                </form>
            </div>
        </DialogContent>
    </Dialog>
</template>
