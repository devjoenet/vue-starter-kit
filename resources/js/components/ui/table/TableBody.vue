<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import { cn } from 'tailwind-variants';
import { tableBodyVariants } from './styles';

const props = defineProps<{
  class?: HTMLAttributes['class'];
}>();
</script>

<template>
  <tbody data-slot="table-body" :class="cn(tableBodyVariants(), props.class)">
    <slot />
  </tbody>
</template>
