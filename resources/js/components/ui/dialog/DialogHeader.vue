<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import { cn } from 'tailwind-variants';
import { dialogHeaderVariants } from './variants';

const props = defineProps<{
  class?: HTMLAttributes['class'];
}>();
</script>

<template>
  <div data-slot="dialog-header" :class="cn(dialogHeaderVariants(), props.class)">
    <slot />
  </div>
</template>
