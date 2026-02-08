<script setup lang="ts">
  import type { HTMLAttributes } from "vue";
  import { cn } from "@/lib/utils";
  import { sidebarMenuItemVariants } from ".";

  const props = defineProps<{
    class?: HTMLAttributes["class"];
  }>();
</script>

<template>
  <li data-slot="sidebar-menu-item" data-sidebar="menu-item" :class="cn(sidebarMenuItemVariants(), props.class)">
    <slot />
  </li>
</template>
