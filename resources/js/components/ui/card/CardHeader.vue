<script setup lang="ts">
  import type { HTMLAttributes } from "vue";
  import { cn } from "@/lib/utils";
  import { cardHeaderVariants } from "./styles";

  const props = defineProps<{
    class?: HTMLAttributes["class"];
  }>();
</script>

<template>
  <div data-slot="card-header" :class="cn(cardHeaderVariants(), props.class)">
    <slot />
  </div>
</template>
