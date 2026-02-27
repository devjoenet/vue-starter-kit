<script lang="ts" setup>
  import type { HTMLAttributes } from "vue";
  import { ChevronRight } from "lucide-vue-next";
  import { cn } from "@/lib/utils";
  import { breadcrumbSeparatorVariants } from "./styles";

  const props = defineProps<{
    class?: HTMLAttributes["class"];
  }>();
</script>

<template>
  <li data-slot="breadcrumb-separator" role="presentation" aria-hidden="true" :class="cn(breadcrumbSeparatorVariants(), props.class)">
    <slot>
      <ChevronRight />
    </slot>
  </li>
</template>
