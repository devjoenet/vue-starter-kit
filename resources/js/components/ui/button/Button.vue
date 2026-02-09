<script setup lang="ts">
  import type { PrimitiveProps } from "reka-ui";
  import type { HTMLAttributes } from "vue";
  import type { ButtonVariants } from ".";
  import { Primitive } from "reka-ui";
  import { cn } from "@/lib/utils";
  import { buttonVariants } from ".";

  interface Props extends PrimitiveProps {
    appearance?: ButtonVariants["appearance"];
    variant?: ButtonVariants["variant"];
    size?: ButtonVariants["size"];
    rounded?: ButtonVariants["rounded"];
    class?: HTMLAttributes["class"];
  }

  const props = withDefaults(defineProps<Props>(), {
    as: "button",
  });
</script>

<template>
  <Primitive data-slot="button" :as="as" :as-child="asChild" :class="cn(buttonVariants({ appearance, variant, size, rounded }), props.class)">
    <slot />
  </Primitive>
</template>
