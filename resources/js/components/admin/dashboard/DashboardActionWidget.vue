<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { cn } from 'tailwind-variants';
import DashboardSurfaceCard from '@/components/admin/dashboard/DashboardSurfaceCard.vue';
import Button from '@/components/ui/button/Button.vue';
import { getCardBadgeClassNames, getCardButtonAppearance, getCardButtonVariant, getCardDividerClassNames, type CardAppearance, type CardVariantName } from '@/components/ui/card/variants';

const props = withDefaults(
  defineProps<{
    appearance: CardAppearance;
    count: number;
    ctaLabel: string;
    description: string;
    emptyDescription: string;
    emptyTitle: string;
    eyebrow: string;
    href: string;
    title: string;
    variant: CardVariantName;
  }>(),
  {
    appearance: 'outline',
    variant: 'muted',
  },
);

const formatCount = (value: number): string => {
  return new Intl.NumberFormat().format(value);
};
</script>

<template>
  <DashboardSurfaceCard :appearance="props.appearance" :variant="props.variant" emphasis="standard" class="px-5 py-5 md:px-6 md:py-6">
    <div class="flex h-full flex-col gap-5">
      <div class="flex items-start justify-between gap-4">
        <div class="min-w-0">
          <p class="text-[0.68rem] font-semibold tracking-[0.18em] text-muted-foreground/82 uppercase">{{ props.eyebrow }}</p>
          <h3 class="mt-3 text-[1.4rem] leading-[1.02] font-semibold tracking-[-0.03em] text-balance">{{ props.title }}</h3>
        </div>

        <span :class="getCardBadgeClassNames({ appearance: props.appearance, variant: props.variant })">
          {{ formatCount(props.count) }}
        </span>
      </div>

      <p class="text-sm leading-6 text-foreground/78">
        {{ props.count === 0 ? props.emptyDescription : props.description }}
      </p>

      <div :class="cn('mt-auto flex items-center justify-between gap-4 pt-4', getCardDividerClassNames({ appearance: props.appearance, variant: props.variant }))">
        <p class="text-sm font-semibold tracking-tight text-foreground/92">
          {{ props.count === 0 ? props.emptyTitle : `${props.title} are live now.` }}
        </p>

        <Button as-child :appearance="getCardButtonAppearance(props.appearance)" :variant="getCardButtonVariant(props.variant)" rounded="full" size="sm">
          <Link :href="props.href" :prefetch="['hover', 'click']">{{ props.ctaLabel }}</Link>
        </Button>
      </div>
    </div>
  </DashboardSurfaceCard>
</template>
