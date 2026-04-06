<script setup lang="ts">
import { cn } from 'tailwind-variants';
import DashboardSurfaceCard from '@/components/admin/dashboard/DashboardSurfaceCard.vue';
import { getCardBadgeClassNames, getCardDividerClassNames, getCardInsetPanelClassNames, type CardAppearance, type CardVariantName } from '@/components/ui/card/variants';

const props = defineProps<{
  appearance: CardAppearance;
  description: string;
  eyebrow: string;
  items: string[];
  title: string;
  variant: CardVariantName;
}>();
</script>

<template>
  <DashboardSurfaceCard :appearance="props.appearance" :variant="props.variant" emphasis="standard" class="px-6 py-6 md:px-8 md:py-7">
    <div class="flex h-full flex-col gap-6">
      <div class="flex items-start justify-between gap-4">
        <div class="max-w-88 space-y-4">
          <p class="section-kicker">{{ props.eyebrow }}</p>
          <h3 class="text-[clamp(1.7rem,2.6vw,2.6rem)] leading-[0.96] font-semibold tracking-[-0.045em] text-balance">
            {{ props.title }}
          </h3>
          <p class="text-sm leading-6 text-foreground/78 sm:text-base sm:leading-7">
            {{ props.description }}
          </p>
        </div>

        <span :class="getCardBadgeClassNames({ appearance: props.appearance, variant: props.variant })"> {{ props.items.length }} notes </span>
      </div>

      <ul class="grid gap-4 lg:grid-cols-3">
        <li v-for="item in props.items" :key="item" :class="cn('flex min-h-[10.75rem] flex-col px-4 py-4', getCardInsetPanelClassNames({ appearance: 'outline', variant: props.variant }))">
          <p class="text-[0.68rem] font-semibold tracking-[0.18em] text-muted-foreground/80 uppercase">Board guardrail</p>
          <p class="mt-4 text-sm leading-6 text-foreground/80">
            {{ item }}
          </p>
          <div :class="cn('mt-auto pt-4', getCardDividerClassNames({ appearance: 'outline', variant: props.variant }))">
            <p class="text-xs font-semibold tracking-[0.16em] text-muted-foreground/82 uppercase">Keep it deliberate</p>
          </div>
        </li>
      </ul>
    </div>
  </DashboardSurfaceCard>
</template>
