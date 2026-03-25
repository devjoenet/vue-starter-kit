<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import { cn } from 'tailwind-variants';
import { tableHeaderVariants } from './styles';

const props = defineProps<{
  class?: HTMLAttributes['class'];
}>();
</script>

<template>
  <thead data-slot="table-header" :class="cn(tableHeaderVariants(), props.class)">
    <slot />
  </thead>
</template>
