<script setup lang="ts">
  import type { HTMLAttributes } from "vue";
  import type { CardVariants } from "./styles";
  import { cn } from "@/lib/utils";
  import { cardVariants } from "./styles";

  const props = withDefaults(
    defineProps<{
      class?: HTMLAttributes["class"];
      variant?: CardVariants["variant"];
    }>(),
    {
      variant: "default",
    },
  );
</script>

<template>
  <div data-slot="card" :class="cn(cardVariants({ variant: props.variant }), props.class)">
    <slot />
  </div>
</template>
