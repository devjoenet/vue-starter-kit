<script setup lang="ts">
  import type { HTMLAttributes } from "vue";
  import { cn } from "@/lib/utils";
  import { sidebarMenuVariants } from ".";

  const props = defineProps<{
    class?: HTMLAttributes["class"];
  }>();
</script>

<template>
  <ul data-slot="sidebar-menu" data-sidebar="menu" :class="cn(sidebarMenuVariants(), props.class)">
    <slot />
  </ul>
</template>
