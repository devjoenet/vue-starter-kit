<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import { cn } from 'tailwind-variants';
import { sheetFooterVariants } from './variants';

const props = defineProps<{ class?: HTMLAttributes['class'] }>();
</script>

<template>
  <div data-slot="sheet-footer" :class="cn(sheetFooterVariants(), props.class)">
    <slot />
  </div>
</template>
