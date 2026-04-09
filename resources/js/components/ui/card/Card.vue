<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import { cn } from 'tailwind-variants';
import type { CardAppearance, CardBorderEffect, CardVariantName } from './variants';
import { getCardBorderTraceClassNames, getCardBorderTraceOverlayClassNames, getCardClassNames } from './variants';

const props = withDefaults(
  defineProps<{
    appearance?: CardAppearance;
    borderEffect?: CardBorderEffect;
    class?: HTMLAttributes['class'];
    variant?: CardVariantName;
  }>(),
  {
    appearance: 'filled',
    borderEffect: 'none',
    variant: 'neutral',
  },
);
</script>

<template>
  <div
    data-slot="card"
    :data-card-style="props.appearance"
    :data-card-variant="props.variant"
    :data-card-border-effect="props.borderEffect"
    :class="cn(getCardClassNames({ appearance: props.appearance, variant: props.variant }), props.borderEffect === 'trace' && 'group/card', props.class)"
  >
    <template v-if="props.borderEffect === 'trace'">
      <span
        aria-hidden="true"
        :class="
          cn(
            'pointer-events-none absolute inset-0 z-1 rounded-[inherit] border opacity-0 transition-opacity duration-700 ease-(--motion-ease-out-quart) group-focus-within/card:opacity-100 group-hover/card:opacity-100 motion-reduce:transition-none',
            getCardBorderTraceOverlayClassNames(props.variant),
          )
        "
      />
      <span
        aria-hidden="true"
        :class="
          cn(
            'pointer-events-none absolute inset-x-0 top-0 z-2 h-0.5 origin-left scale-x-0 transition-transform duration-700 ease-(--motion-ease-out-quart) group-focus-within/card:scale-x-100 group-hover/card:scale-x-100 motion-reduce:transition-none',
            getCardBorderTraceClassNames(props.variant),
          )
        "
      />
      <span
        aria-hidden="true"
        :class="
          cn(
            'pointer-events-none absolute inset-y-0 right-0 z-2 w-0.5 origin-top scale-y-0 transition-transform delay-75 duration-700 ease-(--motion-ease-out-quart) group-focus-within/card:scale-y-100 group-hover/card:scale-y-100 motion-reduce:transition-none motion-reduce:delay-0',
            getCardBorderTraceClassNames(props.variant),
          )
        "
      />
      <span
        aria-hidden="true"
        :class="
          cn(
            'pointer-events-none absolute inset-x-0 bottom-0 z-2 h-0.5 origin-right scale-x-0 transition-transform delay-150 duration-700 ease-(--motion-ease-out-quart) group-focus-within/card:scale-x-100 group-hover/card:scale-x-100 motion-reduce:transition-none motion-reduce:delay-0',
            getCardBorderTraceClassNames(props.variant),
          )
        "
      />
      <span
        aria-hidden="true"
        :class="
          cn(
            'pointer-events-none absolute inset-y-0 left-0 z-2 w-0.5 origin-bottom scale-y-0 transition-transform delay-225 duration-700 ease-(--motion-ease-out-quart) group-focus-within/card:scale-y-100 group-hover/card:scale-y-100 motion-reduce:transition-none motion-reduce:delay-0',
            getCardBorderTraceClassNames(props.variant),
          )
        "
      />
    </template>
    <slot />
  </div>
</template>
