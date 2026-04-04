<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import Button from '@/components/ui/button/Button.vue';
import DashboardSurfaceCard from '@/components/admin/dashboard/DashboardSurfaceCard.vue';

type DashboardAnchorSummaryItem = {
  label: string;
  tone: 'users' | 'roles' | 'permissions';
  value: number;
};

type DashboardAnchorAction = {
  href: string;
  label: string;
};

const props = defineProps<{
  description: string;
  emptyDescription: string;
  eyebrow: string;
  points: string[];
  primaryAction: DashboardAnchorAction | null;
  summaryItems: DashboardAnchorSummaryItem[];
  title: string;
}>();

const formatCount = (value: number): string => {
  return new Intl.NumberFormat().format(value);
};

const chipClassNames: Record<DashboardAnchorSummaryItem['tone'], string> = {
  users: 'border-primary/18 bg-primary/10 text-primary',
  roles: 'border-accent/18 bg-accent/10 text-accent',
  permissions: 'border-secondary/18 bg-secondary/12 text-secondary',
};
</script>

<template>
  <DashboardSurfaceCard tone="neutral" emphasis="hero" class="px-6 py-6 md:px-8 md:py-8">
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
        <div v-for="item in props.summaryItems" :key="item.label" :class="chipClassNames[item.tone]" class="inline-flex items-center gap-3 rounded-full border px-4 py-2 text-sm font-semibold shadow-[var(--elevation-1)]">
          <span class="text-lg tabular-nums">{{ formatCount(item.value) }}</span>
          <span class="text-xs tracking-[0.16em] uppercase">{{ item.label }}</span>
        </div>
      </div>

      <div class="grid flex-1 gap-6 xl:grid-cols-[minmax(0,1fr)_minmax(18rem,20rem)]">
        <ul class="grid gap-3 sm:grid-cols-3">
          <li v-for="point in props.points" :key="point" class="rounded-[1.2rem] border border-border/60 bg-background/28 px-4 py-4 text-sm leading-6 text-muted-foreground">
            {{ point }}
          </li>
        </ul>

        <aside class="rounded-[1.35rem] border border-border/60 bg-background/26 px-5 py-5 shadow-[var(--elevation-1)]">
          <p class="text-[0.68rem] font-semibold tracking-[0.18em] text-muted-foreground/82 uppercase">First move</p>
          <div v-if="props.primaryAction" class="mt-3 space-y-4">
            <p class="text-sm leading-6 text-muted-foreground">Start with the admin surface that changes access right now, then let the rest of the board grow only when it has honest data to show.</p>

            <Button as-child appearance="filled" variant="primary" rounded="full" size="lg" class="w-full sm:w-auto">
              <Link :href="props.primaryAction.href" :prefetch="['hover', 'click']">{{ props.primaryAction.label }}</Link>
            </Button>
          </div>
          <p v-else class="mt-3 text-sm leading-6 text-muted-foreground">
            {{ props.emptyDescription }}
          </p>
        </aside>
      </div>
    </div>
  </DashboardSurfaceCard>
</template>
