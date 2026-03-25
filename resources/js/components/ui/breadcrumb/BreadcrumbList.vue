<script lang="ts" setup>
import type { HTMLAttributes } from 'vue';
import { cn } from 'tailwind-variants';
import { breadcrumbListVariants } from './styles';

const props = defineProps<{
  class?: HTMLAttributes['class'];
}>();
</script>

<template>
  <ol data-slot="breadcrumb-list" :class="cn(breadcrumbListVariants(), props.class)">
    <slot />
  </ol>
</template>
