<script lang="ts" setup>
  import type { HTMLAttributes } from "vue";
  import { cn } from "@/lib/utils";
  import { breadcrumbItemVariants } from ".";

  const props = defineProps<{
    class?: HTMLAttributes["class"];
  }>();
</script>

<template>
  <li data-slot="breadcrumb-item" :class="cn(breadcrumbItemVariants(), props.class)">
    <slot />
  </li>
</template>
