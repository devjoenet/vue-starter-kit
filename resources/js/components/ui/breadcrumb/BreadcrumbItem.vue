<script lang="ts" setup>
import type { HTMLAttributes } from 'vue';
import { cn } from 'tailwind-variants';
import { breadcrumbItemVariants } from './variants';

const props = defineProps<{
  class?: HTMLAttributes['class'];
}>();
</script>

<template>
  <li data-slot="breadcrumb-item" :class="cn(breadcrumbItemVariants(), props.class)">
    <slot />
  </li>
</template>
