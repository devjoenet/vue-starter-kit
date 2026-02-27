<script setup lang="ts">
  import type { HTMLAttributes } from "vue";
  import { cn } from "@/lib/utils";
  import { sheetHeaderVariants } from "./styles";

  const props = defineProps<{ class?: HTMLAttributes["class"] }>();
</script>

<template>
  <div data-slot="sheet-header" :class="cn(sheetHeaderVariants(), props.class)">
    <slot />
  </div>
</template>
