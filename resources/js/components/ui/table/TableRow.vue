<script setup lang="ts">
  import type { HTMLAttributes } from "vue";
  import { cn } from "@/lib/utils";
  import { tableRowVariants } from ".";

  const props = defineProps<{
    class?: HTMLAttributes["class"];
  }>();
</script>

<template>
  <tr data-slot="table-row" :class="cn(tableRowVariants(), props.class)">
    <slot />
  </tr>
</template>
