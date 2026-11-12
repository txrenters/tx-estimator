<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import DeskSidebar from '@/components/desk/DeskSidebar.vue';
import DeskTopbar from '@/components/desk/DeskTopbar.vue';
import FloatingCalculator from '@/components/desk/FloatingCalculator.vue';
import { Toaster } from '@/components/ui/sonner';
import type { DeskProps } from '@/types/desk';

const { crumb = 'Estimates' } = defineProps<{ crumb?: string }>();

const page = usePage();
const desk = computed(() => page.props.desk as DeskProps);
const menuOpen = ref(false);
const location = computed(() => desk.value?.currentCompany?.location ?? 'Greater Houston, TX');
</script>

<template>
    <div class="desk">
        <Toaster position="top-right" rich-colors />
        <FloatingCalculator />
        <div class="shell">
            <DeskSidebar :open="menuOpen" @close="menuOpen = false" />
            <div v-if="menuOpen" class="sidebar-scrim" @click="menuOpen = false" />
            <div class="main">
                <DeskTopbar :crumb="crumb" @menu="menuOpen = !menuOpen" />
                <main class="content">
                    <slot />
                </main>
                <footer class="main-footer">
                    <span>Crystal Creek Estimating Desk</span>
                    <span>{{ location }}<span class="footer-dot">•</span>USD</span>
                </footer>
            </div>
        </div>
    </div>
</template>
