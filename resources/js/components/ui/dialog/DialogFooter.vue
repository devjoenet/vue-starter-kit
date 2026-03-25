<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import { cn } from 'tailwind-variants';
import { dialogFooterVariants } from './variants';

const props = defineProps<{ class?: HTMLAttributes['class'] }>();
</script>

<template>
  <div data-slot="dialog-footer" :class="cn(dialogFooterVariants(), props.class)">
    <slot />
  </div>
</template>
