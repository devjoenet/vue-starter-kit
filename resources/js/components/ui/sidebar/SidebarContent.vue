<script setup lang="ts">
  import type { HTMLAttributes } from "vue";
  import { cn } from "@/lib/utils";
  import { sidebarContentVariants } from ".";

  const props = defineProps<{
    class?: HTMLAttributes["class"];
  }>();
</script>

<template>
  <div data-slot="sidebar-content" data-sidebar="content" :class="cn(sidebarContentVariants(), props.class)">
    <slot />
  </div>
</template>
