<script setup lang="ts">
  import type { HTMLAttributes } from "vue";
  import { cn } from "@/lib/utils";
  import { alertDescriptionVariants } from "./styles";

  const props = defineProps<{
    class?: HTMLAttributes["class"];
  }>();
</script>

<template>
  <div data-slot="alert-description" :class="cn(alertDescriptionVariants(), props.class)">
    <slot />
  </div>
</template>
