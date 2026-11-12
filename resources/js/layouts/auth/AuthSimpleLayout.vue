<script setup lang="ts">
defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div class="desk-auth">
        <div class="desk-auth-card">
            <div class="desk-auth-brand">
                <img src="/favicon.svg" alt="" />
                <div>
                    <strong>CRYSTAL CREEK</strong>
                    <span>ESTIMATING DESK</span>
                </div>
            </div>
            <div class="desk-auth-heading">
                <h1>{{ title }}</h1>
                <p>{{ description }}</p>
            </div>
            <slot />
        </div>
        <p class="desk-auth-foot">Greater Houston, TX • USD</p>
    </div>
</template>
