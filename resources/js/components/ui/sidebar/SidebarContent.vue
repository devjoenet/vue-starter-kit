<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import { cn } from 'tailwind-variants';
import { sidebarContentVariants } from './styles';

const props = defineProps<{
  class?: HTMLAttributes['class'];
}>();
</script>

<template>
  <div data-slot="sidebar-content" data-sidebar="content" :class="cn(sidebarContentVariants(), props.class)">
    <slot />
  </div>
</template>
