<script setup lang="ts">
  import type { DropdownMenuItemProps } from "reka-ui";
  import type { HTMLAttributes } from "vue";
  import type { DropdownMenuItemVariants } from "./styles";
  import { reactiveOmit } from "@vueuse/core";
  import { DropdownMenuItem, useForwardProps } from "reka-ui";
  import { cn } from "@/lib/utils";
  import { dropdownMenuItemVariants } from "./styles";

  const props = withDefaults(
    defineProps<
      DropdownMenuItemProps & {
        class?: HTMLAttributes["class"];
        inset?: DropdownMenuItemVariants["inset"];
        variant?: DropdownMenuItemVariants["variant"];
      }
    >(),
    {
      variant: "default",
    },
  );

  const delegatedProps = reactiveOmit(props, "inset", "variant", "class");

  const forwardedProps = useForwardProps(delegatedProps);
</script>

<template>
  <DropdownMenuItem data-slot="dropdown-menu-item" :data-inset="inset ? '' : undefined" :data-variant="variant" v-bind="forwardedProps" :class="cn(dropdownMenuItemVariants({ variant, inset }), props.class)">
    <slot />
  </DropdownMenuItem>
</template>
