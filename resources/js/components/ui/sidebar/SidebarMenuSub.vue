<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import { cn } from 'tailwind-variants';
import { sidebarMenuSubVariants } from './variants';

const props = defineProps<{
  class?: HTMLAttributes['class'];
}>();
</script>

<template>
  <ul data-slot="sidebar-menu-sub" data-sidebar="menu-badge" :class="cn(sidebarMenuSubVariants(), 'group-data-[collapsible=icon]:hidden', props.class)">
    <slot />
  </ul>
</template>
