<script setup lang="ts">
  import type { HTMLAttributes } from "vue";
  import { cn } from "@/lib/utils";
  import { tableCaptionVariants } from ".";

  const props = defineProps<{
    class?: HTMLAttributes["class"];
  }>();
</script>

<template>
  <caption data-slot="table-caption" :class="cn(tableCaptionVariants(), props.class)">
    <slot />
  </caption>
</template>
