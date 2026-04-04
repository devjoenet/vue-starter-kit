<script setup lang="ts">
import DashboardSurfaceCard from '@/components/admin/dashboard/DashboardSurfaceCard.vue';
import type { DashboardWidgetTone } from '@/types/admin/dashboard';

const props = defineProps<{
  count: number;
  description: string;
  eyebrow: string;
  title: string;
  tone: DashboardWidgetTone;
}>();

const formatCount = (value: number): string => {
  return new Intl.NumberFormat().format(value);
};
</script>

<template>
  <DashboardSurfaceCard :tone="props.tone" emphasis="compact" class="px-5 py-5 md:px-6 md:py-6">
    <div class="flex h-full flex-col justify-between gap-5">
      <div>
        <p class="text-[0.68rem] font-semibold tracking-[0.18em] text-muted-foreground/80 uppercase">{{ props.eyebrow }}</p>
        <p class="mt-3 text-sm font-semibold tracking-tight text-balance">{{ props.title }}</p>
      </div>

      <div class="space-y-2">
        <p class="text-[clamp(2rem,4vw,2.9rem)] leading-none font-semibold tracking-[-0.05em] tabular-nums">{{ formatCount(props.count) }}</p>
        <p class="text-sm leading-6 text-muted-foreground">{{ props.description }}</p>
      </div>
    </div>
  </DashboardSurfaceCard>
</template>
