<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import { cn } from 'tailwind-variants';
import type { AlertVariants } from './variants';
import { alert } from './variants';

const props = defineProps<{
  class?: HTMLAttributes['class'];
  variant?: AlertVariants['variant'];
}>();
</script>

<template>
  <div data-slot="alert" :class="cn(alert({ variant }), props.class)" role="alert">
    <slot />
  </div>
</template>
