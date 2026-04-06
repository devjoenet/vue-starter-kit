<script setup lang="ts">
import { cn } from 'tailwind-variants';
import DashboardSurfaceCard from '@/components/admin/dashboard/DashboardSurfaceCard.vue';
import { getCardBadgeClassNames, type CardAppearance, type CardVariantName } from '@/components/ui/card/variants';

type DashboardAnchorSummaryItem = {
  label: string;
  value: number;
  variant: CardVariantName;
};

const props = defineProps<{
  appearance: CardAppearance;
  description: string;
  eyebrow: string;
  summaryItems: DashboardAnchorSummaryItem[];
  title: string;
  variant: CardVariantName;
}>();

const formatCount = (value: number): string => {
  return new Intl.NumberFormat().format(value);
};
</script>

<template>
  <DashboardSurfaceCard :appearance="props.appearance" :variant="props.variant" emphasis="hero" class="px-6 py-6 md:px-8 md:py-8">
    <div class="flex h-full flex-col gap-8">
      <div class="max-w-3xl space-y-4">
        <p class="section-kicker">{{ props.eyebrow }}</p>
        <h2 class="max-w-4xl text-[clamp(2.4rem,4.7vw,4.55rem)] leading-[0.94] font-semibold tracking-[-0.055em] text-balance">
          {{ props.title }}
        </h2>
        <p class="max-w-2xl text-sm leading-6 text-muted-foreground sm:text-base sm:leading-7">
          {{ props.description }}
        </p>
      </div>

      <div class="flex flex-wrap gap-3">
        <div v-for="item in props.summaryItems" :key="item.label" :class="cn(getCardBadgeClassNames({ appearance: 'tinted', variant: item.variant }), 'items-center gap-3 px-4 py-2 text-sm font-semibold')">
          <span class="text-lg tabular-nums">{{ formatCount(item.value) }}</span>
          <span class="text-xs tracking-[0.16em] uppercase">{{ item.label }}</span>
        </div>
      </div>
    </div>
  </DashboardSurfaceCard>
</template>
