<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import { cn } from 'tailwind-variants';
import { cardContentVariants } from './variants';

const props = defineProps<{
  class?: HTMLAttributes['class'];
}>();
</script>

<template>
  <div data-slot="card-content" :class="cn(cardContentVariants(), props.class)">
    <slot />
  </div>
</template>
