<script setup lang="ts">
import { cn } from 'tailwind-variants';
import type { HTMLAttributes } from 'vue';
import Card from '@/components/ui/card/Card.vue';
import type { CardAppearance, CardBorderEffect, CardVariantName } from '@/components/ui/card/variants';

type DashboardSurfaceEmphasis = 'compact' | 'standard' | 'hero';

const props = withDefaults(
  defineProps<{
    appearance?: CardAppearance;
    borderEffect?: CardBorderEffect;
    class?: HTMLAttributes['class'];
    emphasis?: DashboardSurfaceEmphasis;
    variant?: CardVariantName;
  }>(),
  {
    appearance: 'glow',
    borderEffect: 'trace',
    emphasis: 'standard',
    variant: 'neutral',
  },
);

const emphasisClassNames: Record<DashboardSurfaceEmphasis, string> = {
  compact: 'min-h-[10.5rem]',
  standard: 'min-h-[15rem]',
  hero: 'min-h-[23rem]',
};
</script>

<template>
  <Card data-slot="dashboard-surface-card" :appearance="props.appearance" :border-effect="props.borderEffect" :variant="props.variant" :class="cn(emphasisClassNames[props.emphasis], 'rounded-3xl', props.class)">
    <div class="pointer-events-none absolute inset-px rounded-[calc(1.65rem-1px)] bg-linear-to-b from-white/10 via-transparent to-transparent dark:from-white/6" />
    <div class="relative flex h-full flex-col">
      <slot />
    </div>
  </Card>
</template>
