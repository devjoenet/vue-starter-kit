<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import { cn } from 'tailwind-variants';
import { sidebarGroupContentVariants } from './variants';

const props = defineProps<{
  class?: HTMLAttributes['class'];
}>();
</script>

<template>
  <div data-slot="sidebar-group-content" data-sidebar="group-content" :class="cn(sidebarGroupContentVariants(), props.class)">
    <slot />
  </div>
</template>
