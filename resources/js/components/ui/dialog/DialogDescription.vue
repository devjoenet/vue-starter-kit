<script setup lang="ts">
  import type { DialogDescriptionProps } from "reka-ui";
  import type { HTMLAttributes } from "vue";
  import { reactiveOmit } from "@vueuse/core";
  import { DialogDescription, useForwardProps } from "reka-ui";
  import { cn } from "@/lib/utils";
  import { dialogDescriptionVariants } from ".";

  const props = defineProps<DialogDescriptionProps & { class?: HTMLAttributes["class"] }>();

  const delegatedProps = reactiveOmit(props, "class");

  const forwardedProps = useForwardProps(delegatedProps);
</script>

<template>
  <DialogDescription data-slot="dialog-description" v-bind="forwardedProps" :class="cn(dialogDescriptionVariants(), props.class)">
    <slot />
  </DialogDescription>
</template>
