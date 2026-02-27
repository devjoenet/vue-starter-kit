<script setup lang="ts">
  import type { HTMLAttributes } from "vue";
  import { cn } from "@/lib/utils";
  import Input from "@/components/ui/input/Input.vue";
  import { sidebarInputVariants } from "./styles";

  const props = defineProps<{
    class?: HTMLAttributes["class"];
  }>();
</script>

<template>
  <Input data-slot="sidebar-input" data-sidebar="input" :class="cn(sidebarInputVariants(), props.class)">
    <slot />
  </Input>
</template>
