<script setup lang="ts">
  import type { DropdownMenuSubTriggerProps } from "reka-ui";
  import type { HTMLAttributes } from "vue";
  import { reactiveOmit } from "@vueuse/core";
  import { ChevronRight } from "lucide-vue-next";
  import { DropdownMenuSubTrigger, useForwardProps } from "reka-ui";
  import { cn } from "@/lib/utils";
  import { dropdownMenuSubTriggerVariants } from "./styles";

  const props = defineProps<DropdownMenuSubTriggerProps & { class?: HTMLAttributes["class"]; inset?: boolean }>();

  const delegatedProps = reactiveOmit(props, "class", "inset");
  const forwardedProps = useForwardProps(delegatedProps);
</script>

<template>
  <DropdownMenuSubTrigger data-slot="dropdown-menu-sub-trigger" v-bind="forwardedProps" :class="cn(dropdownMenuSubTriggerVariants({ inset }), props.class)">
    <slot />
    <ChevronRight class="ml-auto size-4" />
  </DropdownMenuSubTrigger>
</template>
