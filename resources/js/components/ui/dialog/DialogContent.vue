<script setup lang="ts">
  import type { DialogContentEmits, DialogContentProps } from "reka-ui";
  import type { HTMLAttributes } from "vue";
  import type { DialogContentVariants } from ".";
  import { reactiveOmit } from "@vueuse/core";
  import { X } from "lucide-vue-next";
  import { DialogClose, DialogContent, DialogPortal, useForwardPropsEmits } from "reka-ui";
  import { cn } from "@/lib/utils";
  import { dialogCloseVariants, dialogContentVariants } from ".";
  import DialogOverlay from "./DialogOverlay.vue";

  defineOptions({
    inheritAttrs: false,
  });

  const props = withDefaults(defineProps<DialogContentProps & { class?: HTMLAttributes["class"]; showCloseButton?: boolean; variant?: DialogContentVariants["variant"] }>(), {
    showCloseButton: true,
    variant: "default",
  });
  const emits = defineEmits<DialogContentEmits>();

  const delegatedProps = reactiveOmit(props, "class", "variant");

  const forwarded = useForwardPropsEmits(delegatedProps, emits);
</script>

<template>
  <DialogPortal>
    <DialogOverlay />
    <DialogContent data-slot="dialog-content" v-bind="{ ...$attrs, ...forwarded }" :class="cn(dialogContentVariants({ variant: props.variant }), props.class)">
      <slot />

      <DialogClose v-if="showCloseButton" data-slot="dialog-close" :class="dialogCloseVariants({ variant: props.variant })">
        <X />
        <span class="sr-only">Close</span>
      </DialogClose>
    </DialogContent>
  </DialogPortal>
</template>
