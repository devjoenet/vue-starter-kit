<script setup lang="ts">
  import type { HTMLAttributes } from "vue";
  import { cn } from "@/lib/utils";
  import { skeletonVariants } from ".";

  interface SkeletonProps {
    class?: HTMLAttributes["class"];
  }

  const props = defineProps<SkeletonProps>();
</script>

<template>
  <div data-slot="skeleton" :class="cn(skeletonVariants(), props.class)" />
</template>
