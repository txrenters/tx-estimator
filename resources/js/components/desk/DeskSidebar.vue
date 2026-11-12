<script setup lang="ts">
import { Link, router, usePage } from '@inertiajs/vue3';
import {
    Building2,
    Check,
    ChevronsUpDown,
    Calculator,
    ClipboardList,
    GitCompareArrows,
    LayoutDashboard,
    LayoutGrid,
    Settings,
    SlidersHorizontal,
    Tags,
    Users,
} from '@lucide/vue';
import type { Component } from 'vue';
import { computed } from 'vue';
import CompanyMark from '@/components/desk/CompanyMark.vue';
import { initials } from '@/lib/format';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import type { DeskProps } from '@/types/desk';

defineProps<{ open: boolean }>();
const emit = defineEmits<{ close: [] }>();

const page = usePage();
const desk = computed(() => page.props.desk as DeskProps);

interface NavItem {
    name: string;
    href: string;
    icon: Component;
    match: string;
}

interface NavGroup {
    label: string;
    items: NavItem[];
}

const groups = computed<NavGroup[]>(() => {
    const can = desk.value.can;
    const isAdmin = can.manageCompanies;
    const item = (name: string, href: string, icon: Component): NavItem => ({ name, href, icon, match: href });

    const work: NavItem[] = [];
    if (can.viewOverview) {
        work.push(item('Overview', '/overview', LayoutDashboard));
    }
    work.push(
        item('Estimates', '/estimates', ClipboardList),
        item('Compare estimates', '/compare', GitCompareArrows),
        item('Calculator', '/calculator', Calculator),
    );

    const catalog = [item('Service templates', '/templates', LayoutGrid), item('Pricing defaults', '/pricing-defaults', SlidersHorizontal)];

    // The company being worked in: managers always; admins once they pick a company.
    const company: NavItem[] = [];
    if (can.manageCompany && desk.value.currentCompany) {
        company.push(item('Company settings', '/company/settings', Settings));
    }
    if (can.manageUsers && !isAdmin) {
        company.push(item('Users', '/users', Users));
    }

    // Everything across companies, for admins.
    const admin: NavItem[] = isAdmin
        ? [item('Companies', '/companies', Building2), item('Services', '/services', Tags), item('Users', '/users', Users)]
        : [];

    return [
        { label: 'WORK', items: work },
        { label: 'CATALOG', items: catalog },
        { label: 'COMPANY', items: company },
        { label: 'ADMIN', items: admin },
    ].filter((g) => g.items.length > 0);
});

const isActive = (match: string) => page.url === match || page.url.startsWith(match + '/') || page.url.startsWith(match + '?');

function switchTo(companyId: number | null) {
    router.post('/current-company', { company_id: companyId }, { preserveScroll: true, onSuccess: () => emit('close') });
}

const pinned = computed(() => (desk.value.can.switchCompany ? null : desk.value.currentCompany));
</script>

<template>
    <aside class="sidebar" :class="{ 'mobile-open': open }">
        <div class="wordmark">
            <img src="/favicon.svg" alt="" />
            <div>
                <strong>CRYSTAL CREEK</strong>
                <span>ESTIMATING DESK</span>
            </div>
        </div>

        <DropdownMenu v-if="desk.can.switchCompany">
            <DropdownMenuTrigger as-child>
                <button
                    type="button"
                    class="company-switcher"
                    :class="{ 'has-logo': desk.currentCompany?.logoUrl }"
                    data-test="company-switcher"
                >
                    <span v-if="desk.currentCompany?.logoUrl" class="switcher-logo">
                        <img :src="desk.currentCompany.logoUrl" :alt="`${desk.currentCompany.name} logo`" />
                    </span>
                    <CompanyMark v-else-if="desk.currentCompany" :company="desk.currentCompany" size="badge" />
                    <span v-else class="workspace-avatar">CC</span>
                    <span class="company-switcher-text">
                        <small>COMPANY</small>
                        <strong>{{ desk.currentCompany?.name ?? 'All companies' }}</strong>
                    </span>
                    <ChevronsUpDown :size="15" />
                </button>
            </DropdownMenuTrigger>
            <DropdownMenuContent class="w-64" align="start" :side-offset="6">
                <DropdownMenuItem class="company-option" @select="switchTo(null)">
                    <span class="option-logo empty"><span class="company-dot" style="background: #a4e9be" /></span>
                    All companies
                    <Check v-if="!desk.currentCompany" class="ml-auto" :size="15" />
                </DropdownMenuItem>
                <DropdownMenuSeparator />
                <DropdownMenuItem v-for="company in desk.companies" :key="company.id" class="company-option" @select="switchTo(company.id)">
                    <span v-if="company.logoUrl" class="option-logo"><img :src="company.logoUrl" alt="" /></span>
                    <span v-else class="option-logo empty"><CompanyMark :company="company" /></span>
                    {{ company.name }}
                    <Check v-if="desk.currentCompany?.id === company.id" class="ml-auto" :size="15" />
                </DropdownMenuItem>
            </DropdownMenuContent>
        </DropdownMenu>
        <div v-else-if="pinned" class="company-switcher static" :class="{ 'has-logo': pinned.logoUrl }">
            <span v-if="pinned.logoUrl" class="switcher-logo">
                <img :src="pinned.logoUrl" :alt="`${pinned.name} logo`" />
            </span>
            <CompanyMark v-else :company="pinned" size="badge" />
            <span class="company-switcher-text">
                <small>COMPANY</small>
                <strong>{{ pinned.name }}</strong>
            </span>
        </div>

        <nav class="side-nav">
            <div v-for="group in groups" :key="group.label" class="side-group">
                <div class="side-label">{{ group.label }}</div>
                <Link
                    v-for="item in group.items"
                    :key="item.name"
                    :href="item.href"
                    as="button"
                    :class="{ active: isActive(item.match) }"
                    @click="emit('close')"
                >
                    <component :is="item.icon" :size="17" />
                    {{ item.name }}
                </Link>
            </div>
        </nav>

        <div class="sidebar-foot">
            <span class="workspace-avatar">{{ initials(desk.user.name) }}</span>
            <div>
                <strong>{{ desk.user.name }}</strong>
                <span>{{ desk.user.roleLabel }}</span>
            </div>
        </div>
    </aside>
</template>
