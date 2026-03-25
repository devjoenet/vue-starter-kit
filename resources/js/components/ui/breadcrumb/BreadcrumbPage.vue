<script lang="ts" setup>
import type { HTMLAttributes } from 'vue';
import { cn } from 'tailwind-variants';
import { breadcrumbPageVariants } from './variants';

const props = defineProps<{
  class?: HTMLAttributes['class'];
}>();
</script>

<template>
  <span data-slot="breadcrumb-page" role="link" aria-disabled="true" aria-current="page" :class="cn(breadcrumbPageVariants(), props.class)">
    <slot />
  </span>
</template>
