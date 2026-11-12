import { createInertiaApp } from '@inertiajs/vue3';
import AuthLayout from '@/layouts/AuthLayout.vue';
import DeskLayout from '@/layouts/DeskLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { initializeFlashToast } from '@/lib/flashToast';

const appName = 'Crystal Creek Estimating Desk';

void createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    layout: (name) => {
        switch (true) {
            case name.startsWith('public/'):
                return null;
            case name.startsWith('auth/'):
                return AuthLayout;
            case name.startsWith('settings/'):
                return [DeskLayout, SettingsLayout];
            default:
                return DeskLayout;
        }
    },
    withApp: (app) => {
        app.directive('focus', {
            mounted: (el: HTMLElement, shouldFocus) => {
                if (shouldFocus.value !== false) {
                    el.focus();
                }
            },
        });
    },
    progress: {
        color: '#17664e',
    },
});

// This will listen for flash toast data from the server...
initializeFlashToast();
