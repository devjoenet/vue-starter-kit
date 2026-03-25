<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import { cn } from 'tailwind-variants';
import { sidebarGroupVariants } from './variants';

const props = defineProps<{
  class?: HTMLAttributes['class'];
}>();
</script>

<template>
  <div data-slot="sidebar-group" data-sidebar="group" :class="cn(sidebarGroupVariants(), props.class)">
    <slot />
  </div>
</template>
