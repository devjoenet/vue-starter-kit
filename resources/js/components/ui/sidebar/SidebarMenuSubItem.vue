<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import { cn } from 'tailwind-variants';
import { sidebarMenuSubItemVariants } from './variants';

const props = defineProps<{
  class?: HTMLAttributes['class'];
}>();
</script>

<template>
  <li data-slot="sidebar-menu-sub-item" data-sidebar="menu-sub-item" :class="cn(sidebarMenuSubItemVariants(), props.class)">
    <slot />
  </li>
</template>
