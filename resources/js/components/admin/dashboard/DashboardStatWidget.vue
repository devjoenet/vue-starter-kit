<script setup lang="ts">
import { cn } from 'tailwind-variants';
import DashboardSurfaceCard from '@/components/admin/dashboard/DashboardSurfaceCard.vue';
import { getCardMetricClassNames, type CardAppearance, type CardVariantName } from '@/components/ui/card/variants';

const props = defineProps<{
  appearance: CardAppearance;
  count: number;
  description: string;
  eyebrow: string;
  title: string;
  variant: CardVariantName;
}>();

const formatCount = (value: number): string => {
  return new Intl.NumberFormat().format(value);
};
</script>

<template>
  <DashboardSurfaceCard :appearance="props.appearance" :variant="props.variant" emphasis="compact" class="px-5 py-5 md:px-6 md:py-6">
    <div class="flex h-full flex-col gap-5">
      <div class="flex items-start justify-between gap-4">
        <div class="min-w-0">
          <p class="text-[0.68rem] font-semibold tracking-[0.18em] text-muted-foreground/80 uppercase">{{ props.eyebrow }}</p>
          <p class="mt-3 text-[1.2rem] leading-[1.05] font-semibold tracking-[-0.03em] text-balance">{{ props.title }}</p>
        </div>

        <p :class="cn('text-[clamp(2.4rem,4vw,3.3rem)] leading-none font-semibold tracking-[-0.06em] tabular-nums', getCardMetricClassNames({ appearance: props.appearance, variant: props.variant }))">
          {{ formatCount(props.count) }}
        </p>
      </div>

      <p class="text-sm leading-6 text-foreground/78">
        {{ props.description }}
      </p>
    </div>
  </DashboardSurfaceCard>
</template>
