<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import { cn } from 'tailwind-variants';
import { tableCaptionVariants } from './variants';

const props = defineProps<{
  class?: HTMLAttributes['class'];
}>();
</script>

<template>
  <caption data-slot="table-caption" :class="cn(tableCaptionVariants(), props.class)">
    <slot />
  </caption>
</template>
