<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import { cn } from 'tailwind-variants';
import { sidebarHeaderVariants } from './styles';

const props = defineProps<{
  class?: HTMLAttributes['class'];
}>();
</script>

<template>
  <div data-slot="sidebar-header" data-sidebar="header" :class="cn(sidebarHeaderVariants(), props.class)">
    <slot />
  </div>
</template>
