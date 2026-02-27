<script setup lang="ts">
  import type { TooltipContentEmits, TooltipContentProps } from "reka-ui";
  import type { HTMLAttributes } from "vue";
  import type { TooltipContentVariants } from "./styles";
  import { reactiveOmit } from "@vueuse/core";
  import { TooltipArrow, TooltipContent, TooltipPortal, useForwardPropsEmits } from "reka-ui";
  import { cn } from "@/lib/utils";
  import { tooltipArrowVariants, tooltipContentVariants } from "./styles";

  defineOptions({
    inheritAttrs: false,
  });

  const props = withDefaults(defineProps<TooltipContentProps & { class?: HTMLAttributes["class"]; variant?: TooltipContentVariants["variant"] }>(), {
    sideOffset: 4,
    variant: "default",
  });

  const emits = defineEmits<TooltipContentEmits>();

  const delegatedProps = reactiveOmit(props, "class", "variant");
  const forwarded = useForwardPropsEmits(delegatedProps, emits);
</script>

<template>
  <TooltipPortal>
    <TooltipContent data-slot="tooltip-content" v-bind="{ ...forwarded, ...$attrs }" :class="cn(tooltipContentVariants({ variant: props.variant }), props.class)">
      <slot />

      <TooltipArrow :class="tooltipArrowVariants({ variant: props.variant })" />
    </TooltipContent>
  </TooltipPortal>
</template>
