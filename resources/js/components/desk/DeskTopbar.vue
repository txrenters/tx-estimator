<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { ChevronDown, ChevronRight, Menu } from '@lucide/vue';
import { computed } from 'vue';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import UserMenuContent from '@/components/UserMenuContent.vue';
import type { DeskProps } from '@/types/desk';

defineProps<{ crumb: string }>();
const emit = defineEmits<{ menu: [] }>();

const page = usePage();
const desk = computed(() => page.props.desk as DeskProps);
const scope = computed(
    () => desk.value.currentCompany?.name ?? 'All companies',
);
</script>

<template>
    <header class="topbar">
        <div class="crumb">
            <button
                type="button"
                class="icon-button mobile-menu"
                aria-label="Open menu"
                @click="emit('menu')"
            >
                <Menu :size="18" />
            </button>
            <span>Workspace</span>
            <ChevronRight :size="14" />
            <strong>{{ crumb }}</strong>
        </div>
        <div class="topbar-right">
            <span class="private"><i />{{ scope }}</span>
            <DropdownMenu>
                <DropdownMenuTrigger as-child>
                    <button
                        type="button"
                        class="user-button"
                        data-test="desk-user-menu"
                    >
                        <span>{{ desk.user.name }}</span>
                        <small>{{ desk.user.roleLabel }}</small>
                        <ChevronDown :size="14" />
                    </button>
                </DropdownMenuTrigger>
                <DropdownMenuContent
                    class="min-w-56 rounded-lg"
                    align="end"
                    :side-offset="6"
                >
                    <UserMenuContent :user="page.props.auth.user" />
                </DropdownMenuContent>
            </DropdownMenu>
        </div>
    </header>
</template>
