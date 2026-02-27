<script setup lang="ts">
  import type { HTMLAttributes } from "vue";
  import { cn } from "@/lib/utils";
  import { sidebarFooterVariants } from "./styles";

  const props = defineProps<{
    class?: HTMLAttributes["class"];
  }>();
</script>

<template>
  <div data-slot="sidebar-footer" data-sidebar="footer" :class="cn(sidebarFooterVariants(), props.class)">
    <slot />
  </div>
</template>
