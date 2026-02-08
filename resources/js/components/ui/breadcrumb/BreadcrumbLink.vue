<script lang="ts" setup>
  import type { PrimitiveProps } from "reka-ui";
  import type { HTMLAttributes } from "vue";
  import { Primitive } from "reka-ui";
  import { cn } from "@/lib/utils";
  import { breadcrumbLinkVariants } from ".";

  const props = withDefaults(defineProps<PrimitiveProps & { class?: HTMLAttributes["class"] }>(), {
    as: "a",
  });
</script>

<template>
  <Primitive data-slot="breadcrumb-link" :as="as" :as-child="asChild" :class="cn(breadcrumbLinkVariants(), props.class)">
    <slot />
  </Primitive>
</template>
