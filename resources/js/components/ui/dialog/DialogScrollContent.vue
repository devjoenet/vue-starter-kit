<script setup lang="ts">
  import type { DialogContentEmits, DialogContentProps } from "reka-ui";
  import type { HTMLAttributes } from "vue";
  import type { DialogContentVariants } from "./styles";
  import { reactiveOmit } from "@vueuse/core";
  import { X } from "lucide-vue-next";
  import { DialogClose, DialogContent, DialogOverlay, DialogPortal, useForwardPropsEmits } from "reka-ui";
  import { cn } from "@/lib/utils";
  import { dialogScrollCloseVariants, dialogScrollContentVariants, dialogScrollOverlayVariants } from "./styles";

  defineOptions({
    inheritAttrs: false,
  });

  const props = withDefaults(defineProps<DialogContentProps & { class?: HTMLAttributes["class"]; variant?: DialogContentVariants["variant"] }>(), {
    variant: "default",
  });
  const emits = defineEmits<DialogContentEmits>();

  const delegatedProps = reactiveOmit(props, "class", "variant");

  const forwarded = useForwardPropsEmits(delegatedProps, emits);
</script>

<template>
  <DialogPortal>
    <DialogOverlay :class="dialogScrollOverlayVariants()">
      <DialogContent
        :class="cn(dialogScrollContentVariants({ variant: props.variant }), props.class)"
        v-bind="{ ...$attrs, ...forwarded }"
        @pointer-down-outside="
          (event) => {
            const originalEvent = event.detail.originalEvent;
            const target = originalEvent.target as HTMLElement;
            if (originalEvent.offsetX > target.clientWidth || originalEvent.offsetY > target.clientHeight) {
              event.preventDefault();
            }
          }
        "
      >
        <slot />

        <DialogClose :class="dialogScrollCloseVariants({ variant: props.variant })">
          <X class="w-4 h-4" />
          <span class="sr-only">Close</span>
        </DialogClose>
      </DialogContent>
    </DialogOverlay>
  </DialogPortal>
</template>
