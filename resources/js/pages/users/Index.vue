<script setup lang="ts">
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { Check, Pencil, Plus, UserPlus } from '@lucide/vue';
import { computed, ref } from 'vue';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogTitle,
} from '@/components/ui/dialog';
import type { DeskProps, Role } from '@/types/desk';

defineOptions({ layout: { crumb: 'Users' } });

interface UserRow {
    id: number;
    name: string;
    email: string;
    role: Role;
    roleLabel: string;
    companyId: number | null;
    company: string | null;
    isActive: boolean;
    canEdit: boolean;
    canToggle: boolean;
}

const props = defineProps<{
    users: UserRow[];
    roles: Role[];
    filters: { company: string | number };
}>();

const desk = computed(() => usePage().props.desk as DeskProps);
const inviteError = computed(
    () => (usePage().props.errors as Record<string, string>).invite,
);
const isGlobal = computed(() => desk.value.can.switchCompany);
const labels: Record<Role, string> = {
    superadmin: 'Superadmin',
    admin: 'Admin',
    manager: 'Manager',
    estimator: 'Estimator',
};

const editing = ref<UserRow | null>(null);
const dialogOpen = ref(false);
const form = useForm<{
    name: string;
    email: string;
    role: Role;
    company_id: number | null;
}>({
    name: '',
    email: '',
    role: 'estimator',
    company_id: desk.value.currentCompany?.id ?? null,
});

const needsCompany = computed(
    () =>
        isGlobal.value &&
        (form.role === 'manager' || form.role === 'estimator'),
);

function startCreate() {
    editing.value = null;
    form.reset();
    form.clearErrors();
    dialogOpen.value = true;
}

function startEdit(user: UserRow) {
    editing.value = user;
    form.clearErrors();
    form.name = user.name;
    form.email = user.email;
    form.role = user.role;
    form.company_id = user.companyId;
    dialogOpen.value = true;
}

function save() {
    const options = {
        preserveScroll: true,
        onSuccess: () => (dialogOpen.value = false),
    };
    if (editing.value) {
        form.put(`/users/${editing.value.id}`, options);
    } else {
        form.post('/users', options);
    }
}

function resend(user: UserRow) {
    router.post(`/users/${user.id}/invite`, {}, { preserveScroll: true });
}

function toggle(user: UserRow) {
    router.patch(`/users/${user.id}/active`, {}, { preserveScroll: true });
}

const filterCompany = ref(props.filters.company ?? '');
function applyFilter() {
    router.get(
        '/users',
        { company: filterCompany.value || undefined },
        { preserveState: true, replace: true },
    );
}
</script>

<template>
    <Head title="Users" />
    <div class="page-heading">
        <div>
            <span class="eyebrow">TEAM</span>
            <h1>Users</h1>
            <p v-if="isGlobal">
                Invite people and choose which company they work in.
            </p>
            <p v-else>
                Invite estimators and managers to
                {{ desk.currentCompany?.name }}.
            </p>
        </div>
        <div class="actions">
            <button
                type="button"
                class="button primary"
                data-test="invite-user"
                @click="startCreate"
            >
                <UserPlus :size="16" /> Invite user
            </button>
        </div>
    </div>

    <section class="panel">
        <div v-if="isGlobal" class="filters" style="padding-top: 15px">
            <div class="selects">
                <select
                    v-model="filterCompany"
                    aria-label="Company filter"
                    @change="applyFilter"
                >
                    <option value="">All companies</option>
                    <option
                        v-for="c in desk.companies"
                        :key="c.id"
                        :value="c.id"
                    >
                        {{ c.name }}
                    </option>
                </select>
            </div>
        </div>
        <div class="scroll">
            <table class="estimate-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Role</th>
                        <th>Company</th>
                        <th>Status</th>
                        <th />
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="u in users" :key="u.id">
                        <td>
                            <strong>{{ u.name }}</strong>
                            <small>{{ u.email }}</small>
                        </td>
                        <td>{{ u.roleLabel }}</td>
                        <td>{{ u.company ?? 'All companies' }}</td>
                        <td>
                            <span
                                class="badge"
                                :class="u.isActive ? 'approved' : 'declined'"
                                ><i />{{
                                    u.isActive ? 'Active' : 'Deactivated'
                                }}</span
                            >
                        </td>
                        <td>
                            <span class="row-actions">
                                <button
                                    v-if="u.canEdit"
                                    type="button"
                                    class="icon-button"
                                    :aria-label="`Edit ${u.name}`"
                                    @click="startEdit(u)"
                                >
                                    <Pencil :size="15" />
                                </button>
                                <button
                                    v-if="u.canEdit && u.isActive"
                                    type="button"
                                    class="text-button"
                                    @click="resend(u)"
                                >
                                    Resend invite
                                </button>
                                <button
                                    v-if="u.canToggle"
                                    type="button"
                                    class="text-button"
                                    @click="toggle(u)"
                                >
                                    {{
                                        u.isActive ? 'Deactivate' : 'Reactivate'
                                    }}
                                </button>
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <p v-if="inviteError" class="notice bad" style="margin: 10px 19px">
            {{ inviteError }}
        </p>
        <div class="table-foot">
            <span>{{ users.length }} users</span>
        </div>
    </section>

    <Dialog v-model:open="dialogOpen">
        <DialogContent class="desk-dialog sm:max-w-lg" data-test="user-dialog">
            <div class="desk">
                <DialogTitle as="h2">{{
                    editing ? `Edit ${editing.name}` : 'Invite a user'
                }}</DialogTitle>
                <DialogDescription class="muted">
                    {{
                        editing
                            ? 'Change their details or role.'
                            : 'They receive an email with a link to set their password.'
                    }}
                </DialogDescription>
                <form class="fields" @submit.prevent="save">
                    <label class="wide">
                        Name
                        <input
                            v-model="form.name"
                            autocomplete="off"
                            autofocus
                        />
                        <span v-if="form.errors.name" class="form-error">{{
                            form.errors.name
                        }}</span>
                    </label>
                    <label class="wide">
                        Email
                        <input
                            v-model="form.email"
                            type="email"
                            autocomplete="off"
                        />
                        <span v-if="form.errors.email" class="form-error">{{
                            form.errors.email
                        }}</span>
                    </label>
                    <label :class="{ wide: !needsCompany }">
                        Role
                        <select v-model="form.role">
                            <option v-for="r in roles" :key="r" :value="r">
                                {{ labels[r] }}
                            </option>
                        </select>
                        <span v-if="form.errors.role" class="form-error">{{
                            form.errors.role
                        }}</span>
                    </label>
                    <label v-if="needsCompany">
                        Company
                        <select v-model="form.company_id">
                            <option :value="null" disabled>
                                Choose a company
                            </option>
                            <option
                                v-for="c in desk.companies"
                                :key="c.id"
                                :value="c.id"
                            >
                                {{ c.name }}
                            </option>
                        </select>
                        <span
                            v-if="form.errors.company_id"
                            class="form-error"
                            >{{ form.errors.company_id }}</span
                        >
                    </label>
                    <div class="actions wide dialog-actions">
                        <button
                            type="button"
                            class="button secondary"
                            @click="dialogOpen = false"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="button primary"
                            :disabled="form.processing"
                        >
                            <Check v-if="editing" :size="16" /><Plus
                                v-else
                                :size="16"
                            />
                            {{ editing ? 'Save user' : 'Send invitation' }}
                        </button>
                    </div>
                </form>
            </div>
        </DialogContent>
    </Dialog>
</template>
