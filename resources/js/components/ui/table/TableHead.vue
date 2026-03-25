<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import { cn } from 'tailwind-variants';
import { tableHeadVariants } from './styles';

const props = defineProps<{
  class?: HTMLAttributes['class'];
}>();
</script>

<template>
  <th data-slot="table-head" :class="cn(tableHeadVariants(), props.class)">
    <slot />
  </th>
</template>
