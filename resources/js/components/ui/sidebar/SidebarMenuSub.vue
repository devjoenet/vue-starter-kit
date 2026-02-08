<script setup lang="ts">
  import type { HTMLAttributes } from "vue";
  import { cn } from "@/lib/utils";
  import { sidebarMenuSubVariants } from ".";

  const props = defineProps<{
    class?: HTMLAttributes["class"];
  }>();
</script>

<template>
  <ul data-slot="sidebar-menu-sub" data-sidebar="menu-badge" :class="cn(sidebarMenuSubVariants(), 'group-data-[collapsible=icon]:hidden', props.class)">
    <slot />
  </ul>
</template>
