<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import { cn } from 'tailwind-variants';
import { sheetHeaderVariants } from './variants';

const props = defineProps<{ class?: HTMLAttributes['class'] }>();
</script>

<template>
  <div data-slot="sheet-header" :class="cn(sheetHeaderVariants(), props.class)">
    <slot />
  </div>
</template>
