<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import { cn } from 'tailwind-variants';

type DashboardSurfaceTone = 'neutral' | 'users' | 'roles' | 'permissions';
type DashboardSurfaceEmphasis = 'compact' | 'standard' | 'hero';

const props = withDefaults(
  defineProps<{
    class?: HTMLAttributes['class'];
    emphasis?: DashboardSurfaceEmphasis;
    tone?: DashboardSurfaceTone;
  }>(),
  {
    emphasis: 'standard',
    tone: 'neutral',
  },
);

const toneClassNames: Record<DashboardSurfaceTone, string> = {
  neutral: 'border-border/70 bg-[linear-gradient(155deg,color-mix(in_oklab,var(--surface-panel)_90%,var(--background)_10%)_0%,color-mix(in_oklab,var(--surface-shell)_78%,var(--background)_22%)_100%)]',
  users: 'border-primary/18 bg-[linear-gradient(150deg,color-mix(in_oklab,var(--surface-panel-primary)_78%,var(--primary)_22%)_0%,color-mix(in_oklab,var(--surface-panel)_88%,var(--surface-shell)_12%)_100%)]',
  roles: 'border-accent/18 bg-[linear-gradient(150deg,color-mix(in_oklab,var(--surface-panel-primary)_74%,var(--accent)_26%)_0%,color-mix(in_oklab,var(--surface-panel)_86%,var(--background)_14%)_100%)]',
  permissions: 'border-secondary/18 bg-[linear-gradient(150deg,color-mix(in_oklab,var(--surface-panel-secondary)_80%,var(--secondary)_20%)_0%,color-mix(in_oklab,var(--surface-panel)_88%,var(--background)_12%)_100%)]',
};

const emphasisClassNames: Record<DashboardSurfaceEmphasis, string> = {
  compact: 'min-h-[10.5rem]',
  standard: 'min-h-[15rem]',
  hero: 'min-h-[23rem]',
};
</script>

<template>
  <section :data-dashboard-tone="props.tone" :class="cn('relative flex h-full flex-col overflow-hidden rounded-[1.65rem] border shadow-[var(--elevation-1)]', toneClassNames[props.tone], emphasisClassNames[props.emphasis], props.class)">
    <div class="pointer-events-none absolute inset-0 bg-linear-to-b from-white/10 via-transparent to-transparent dark:from-white/6" />
    <div class="pointer-events-none absolute inset-x-0 top-0 h-20 bg-linear-to-r from-white/12 via-transparent to-transparent opacity-75" />

    <div class="relative z-10 flex h-full flex-col">
      <slot />
    </div>
  </section>
</template>
