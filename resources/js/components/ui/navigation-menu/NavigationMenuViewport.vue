<script setup lang="ts">
  import type { NavigationMenuViewportProps } from "reka-ui";
  import type { HTMLAttributes } from "vue";
  import { reactiveOmit } from "@vueuse/core";
  import { NavigationMenuViewport, useForwardProps } from "reka-ui";
  import { cn } from "@/lib/utils";
  import { navigationMenuViewportContainerVariants, navigationMenuViewportVariants } from "./styles";

  const props = defineProps<NavigationMenuViewportProps & { class?: HTMLAttributes["class"] }>();

  const delegatedProps = reactiveOmit(props, "class");

  const forwardedProps = useForwardProps(delegatedProps);
</script>

<template>
  <div :class="navigationMenuViewportContainerVariants()">
    <NavigationMenuViewport data-slot="navigation-menu-viewport" v-bind="forwardedProps" :class="cn(navigationMenuViewportVariants(), props.class)" />
  </div>
</template>
