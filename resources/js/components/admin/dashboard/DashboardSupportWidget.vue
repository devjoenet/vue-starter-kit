<script setup lang="ts">
import DashboardSurfaceCard from '@/components/admin/dashboard/DashboardSurfaceCard.vue';

const props = defineProps<{
  description: string;
  eyebrow: string;
  items: string[];
  title: string;
}>();
</script>

<template>
  <DashboardSurfaceCard tone="neutral" emphasis="standard" class="px-6 py-6 md:px-8 md:py-7">
    <div class="grid gap-8 lg:grid-cols-[minmax(0,19rem)_minmax(0,1fr)] lg:gap-10">
      <div class="max-w-[19rem] space-y-4 border-t border-border/55 pt-4">
        <p class="section-kicker">{{ props.eyebrow }}</p>
        <h3 class="text-[clamp(1.55rem,2.4vw,2.35rem)] leading-[0.98] font-semibold tracking-[-0.04em] text-balance">
          {{ props.title }}
        </h3>
        <p class="text-sm leading-6 text-muted-foreground sm:text-base sm:leading-7">
          {{ props.description }}
        </p>
      </div>

      <ul class="grid gap-3 lg:grid-cols-3">
        <li v-for="item in props.items" :key="item" class="rounded-[1.2rem] border border-border/60 bg-background/24 px-4 py-4 text-sm leading-6 text-muted-foreground">
          {{ item }}
        </li>
      </ul>
    </div>
  </DashboardSurfaceCard>
</template>
