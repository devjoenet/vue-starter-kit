<script setup lang="ts">
  import type { CheckboxRootEmits, CheckboxRootProps } from "reka-ui";
  import type { HTMLAttributes } from "vue";
  import type { CheckboxVariants } from ".";
  import { reactiveOmit } from "@vueuse/core";
  import { Check } from "lucide-vue-next";
  import { CheckboxIndicator, CheckboxRoot, useForwardPropsEmits } from "reka-ui";
  import { cn } from "@/lib/utils";
  import { checkboxVariants } from ".";

  const props = withDefaults(defineProps<CheckboxRootProps & { class?: HTMLAttributes["class"]; variant?: CheckboxVariants["variant"] }>(), {
    variant: "default",
  });
  const emits = defineEmits<CheckboxRootEmits>();

  const delegatedProps = reactiveOmit(props, "class", "variant");

  const forwarded = useForwardPropsEmits(delegatedProps, emits);
</script>

<template>
  <CheckboxRoot v-slot="slotProps" data-slot="checkbox" v-bind="forwarded" :class="cn(checkboxVariants({ variant: props.variant }), props.class)">
    <CheckboxIndicator data-slot="checkbox-indicator" class="grid place-content-center text-current transition-none">
      <slot v-bind="slotProps">
        <Check class="size-3.5" />
      </slot>
    </CheckboxIndicator>
  </CheckboxRoot>
</template>
