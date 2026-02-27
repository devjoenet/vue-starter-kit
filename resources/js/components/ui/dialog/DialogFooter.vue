<script setup lang="ts">
  import type { HTMLAttributes } from "vue";
  import { cn } from "@/lib/utils";
  import { dialogFooterVariants } from "./styles";

  const props = defineProps<{ class?: HTMLAttributes["class"] }>();
</script>

<template>
  <div data-slot="dialog-footer" :class="cn(dialogFooterVariants(), props.class)">
    <slot />
  </div>
</template>
