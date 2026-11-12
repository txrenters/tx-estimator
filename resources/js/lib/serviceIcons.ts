import { Bug, Droplet, Flame, Hammer, House, Leaf, Paintbrush, Sparkles, Trees, Wind, Wrench, Zap } from '@lucide/vue';
import type { Component } from 'vue';

/** Icons an admin can pick for a trade (must match ServiceGroup::ICONS). */
export const SERVICE_ICONS: Record<string, Component> = {
    wind: Wind,
    bug: Bug,
    house: House,
    wrench: Wrench,
    zap: Zap,
    droplet: Droplet,
    leaf: Leaf,
    paintbrush: Paintbrush,
    hammer: Hammer,
    sparkles: Sparkles,
    flame: Flame,
    trees: Trees,
};

export const serviceIcon = (name: string): Component => SERVICE_ICONS[name] ?? Wrench;
