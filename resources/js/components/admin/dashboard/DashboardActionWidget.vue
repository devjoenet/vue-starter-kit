<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import Button from '@/components/ui/button/Button.vue';
import DashboardSurfaceCard from '@/components/admin/dashboard/DashboardSurfaceCard.vue';
import type { DashboardWidgetTone } from '@/types/admin/dashboard';

const props = defineProps<{
  count: number;
  ctaLabel: string;
  description: string;
  emptyDescription: string;
  emptyTitle: string;
  eyebrow: string;
  href: string;
  title: string;
  tone: DashboardWidgetTone;
}>();

const formatCount = (value: number): string => {
  return new Intl.NumberFormat().format(value);
};

const buttonVariantMap: Record<DashboardWidgetTone, 'muted' | 'primary' | 'secondary'> = {
  neutral: 'muted',
  permissions: 'secondary',
  roles: 'primary',
  users: 'primary',
};
</script>

<template>
  <DashboardSurfaceCard :tone="props.tone" emphasis="standard" class="px-5 py-5 md:px-6 md:py-6">
    <div class="flex h-full flex-col gap-5">
      <div class="flex items-start justify-between gap-4">
        <div class="min-w-0">
          <p class="text-[0.68rem] font-semibold tracking-[0.18em] text-muted-foreground/82 uppercase">{{ props.eyebrow }}</p>
          <h3 class="mt-3 text-[1.4rem] leading-[1.02] font-semibold tracking-[-0.03em] text-balance">{{ props.title }}</h3>
        </div>

        <span class="inline-flex min-w-13 justify-center rounded-full border border-white/12 bg-background/38 px-3 py-1.5 text-sm font-semibold tabular-nums shadow-[var(--elevation-1)]">
          {{ formatCount(props.count) }}
        </span>
      </div>

      <p class="text-sm leading-6 text-muted-foreground">
        {{ props.count === 0 ? props.emptyDescription : props.description }}
      </p>

      <div class="mt-auto flex items-center justify-between gap-4 border-t border-border/55 pt-4">
        <p class="text-sm font-semibold tracking-tight">
          {{ props.count === 0 ? props.emptyTitle : `${props.title} are live now.` }}
        </p>

        <Button as-child appearance="tonal" :variant="buttonVariantMap[props.tone]" rounded="full" size="sm">
          <Link :href="props.href" :prefetch="['hover', 'click']">{{ props.ctaLabel }}</Link>
        </Button>
      </div>
    </div>
  </DashboardSurfaceCard>
</template>
