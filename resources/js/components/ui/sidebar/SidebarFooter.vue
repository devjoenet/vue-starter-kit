<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import { cn } from 'tailwind-variants';
import { sidebarFooterVariants } from './variants';

const props = defineProps<{
  class?: HTMLAttributes['class'];
}>();
</script>

<template>
  <div data-slot="sidebar-footer" data-sidebar="footer" :class="cn(sidebarFooterVariants(), props.class)">
    <slot />
  </div>
</template>
