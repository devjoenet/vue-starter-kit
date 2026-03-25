<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import { cn } from 'tailwind-variants';
import { tableCellVariants } from './variants';

const props = defineProps<{
  class?: HTMLAttributes['class'];
}>();
</script>

<template>
  <td data-slot="table-cell" :class="cn(tableCellVariants(), props.class)">
    <slot />
  </td>
</template>
