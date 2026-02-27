<script setup lang="ts">
  import type { HTMLAttributes } from "vue";
  import { cn } from "@/lib/utils";
  import Separator from "@/components/ui/separator/Separator.vue";
  import { sidebarSeparatorVariants } from "./styles";

  const props = defineProps<{
    class?: HTMLAttributes["class"];
  }>();
</script>

<template>
  <Separator data-slot="sidebar-separator" data-sidebar="separator" :class="cn(sidebarSeparatorVariants(), props.class)">
    <slot />
  </Separator>
</template>
