<script setup lang="ts">
  import type { HTMLAttributes } from "vue";
  import { cn } from "@/lib/utils";
  import { cardTitleVariants } from ".";

  const props = defineProps<{
    class?: HTMLAttributes["class"];
  }>();
</script>

<template>
  <h3 data-slot="card-title" :class="cn(cardTitleVariants(), props.class)">
    <slot />
  </h3>
</template>
