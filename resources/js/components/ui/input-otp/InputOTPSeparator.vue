<script setup lang="ts">
  import type { HTMLAttributes } from "vue";
  import { reactiveOmit } from "@vueuse/core";
  import { MinusIcon } from "lucide-vue-next";
  import { useForwardProps } from "reka-ui";
  import { cn } from "@/lib/utils";
  import { inputOtpSeparatorVariants } from "./styles";

  const props = defineProps<{ class?: HTMLAttributes["class"] }>();

  const delegatedProps = reactiveOmit(props, "class");
  const forwarded = useForwardProps(delegatedProps);
</script>

<template>
  <div data-slot="input-otp-separator" role="separator" v-bind="forwarded" :class="cn(inputOtpSeparatorVariants(), props.class)">
    <slot>
      <MinusIcon />
    </slot>
  </div>
</template>
