<script setup lang="ts">
  import type { HTMLAttributes } from "vue";
  import { cn } from "@/lib/utils";
  import { sidebarMenuSubItemVariants } from "./styles";

  const props = defineProps<{
    class?: HTMLAttributes["class"];
  }>();
</script>

<template>
  <li data-slot="sidebar-menu-sub-item" data-sidebar="menu-sub-item" :class="cn(sidebarMenuSubItemVariants(), props.class)">
    <slot />
  </li>
</template>
