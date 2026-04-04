<script setup lang="ts">
import { computed } from 'vue';
import DashboardSurfaceCard from '@/components/admin/dashboard/DashboardSurfaceCard.vue';
import type { DashboardWidgetTone } from '@/types/admin/dashboard';

const props = withDefaults(
  defineProps<{
    description: string;
    emptyDescription: string;
    emptyTitle: string;
    points: number[];
    title: string;
    tone?: DashboardWidgetTone;
  }>(),
  {
    tone: 'neutral',
  },
);

const chartPoints = computed(() => {
  if (props.points.length < 2) {
    return '';
  }

  const min = Math.min(...props.points);
  const max = Math.max(...props.points);
  const range = max - min || 1;

  return props.points
    .map((point, index) => {
      const x = (index / (props.points.length - 1)) * 100;
      const y = 100 - ((point - min) / range) * 100;

      return `${x},${y}`;
    })
    .join(' ');
});
</script>

<template>
  <DashboardSurfaceCard :tone="props.tone" emphasis="standard" class="px-5 py-5 md:px-6 md:py-6">
    <div class="flex h-full flex-col gap-5">
      <div class="space-y-3">
        <p class="text-[0.68rem] font-semibold tracking-[0.18em] text-muted-foreground/80 uppercase">Trend</p>
        <h3 class="text-[1.4rem] leading-[1.02] font-semibold tracking-[-0.03em] text-balance">{{ props.title }}</h3>
        <p class="text-sm leading-6 text-muted-foreground">
          {{ props.points.length > 1 ? props.description : props.emptyDescription }}
        </p>
      </div>

      <div v-if="props.points.length > 1" class="mt-auto overflow-hidden rounded-[1.2rem] border border-border/60 bg-background/24 p-4">
        <svg viewBox="0 0 100 100" class="h-36 w-full" preserveAspectRatio="none" aria-hidden="true">
          <polyline fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="4" :points="chartPoints" class="text-primary" />
        </svg>
      </div>
      <div v-else class="mt-auto rounded-[1.2rem] border border-dashed border-border/70 bg-background/18 px-4 py-5">
        <p class="text-sm font-semibold tracking-tight">{{ props.emptyTitle }}</p>
      </div>
    </div>
  </DashboardSurfaceCard>
</template>
