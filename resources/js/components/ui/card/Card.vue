<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import type { CardVariants } from './variants';
import { cn } from 'tailwind-variants';
import { cardVariants } from './variants';

const props = withDefaults(
  defineProps<{
    class?: HTMLAttributes['class'];
    variant?: CardVariants['variant'];
  }>(),
  {
    variant: 'default',
  },
);
</script>

<template>
  <div data-slot="card" :class="cn(cardVariants({ variant: props.variant }), props.class)">
    <slot />
  </div>
</template>
