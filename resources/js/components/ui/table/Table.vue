<script setup lang="ts">
  import type { HTMLAttributes } from "vue";
  import { cn } from "@/lib/utils";
  import { tableVariants, tableWrapperVariants } from "./styles";

  const props = defineProps<{
    class?: HTMLAttributes["class"];
    wrapperClass?: HTMLAttributes["class"];
  }>();
</script>

<template>
  <div data-slot="table-wrapper" :class="cn(tableWrapperVariants(), props.wrapperClass)">
    <table data-slot="table" :class="cn(tableVariants(), props.class)">
      <slot />
    </table>
  </div>
</template>
