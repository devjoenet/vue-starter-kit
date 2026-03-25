<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import { cn } from 'tailwind-variants';
import { cardFooterVariants } from './variants';

const props = defineProps<{
  class?: HTMLAttributes['class'];
}>();
</script>

<template>
  <div data-slot="card-footer" :class="cn(cardFooterVariants(), props.class)">
    <slot />
  </div>
</template>
