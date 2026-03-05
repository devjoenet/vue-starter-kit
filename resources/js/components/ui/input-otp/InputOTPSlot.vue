<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import { reactiveOmit } from '@vueuse/core';
import { useForwardProps } from 'reka-ui';
import { computed } from 'vue';
import { useVueOTPContext } from 'vue-input-otp';
import { cn } from '@/lib/utils';
import { inputOtpSlotVariants } from './styles';

const props = defineProps<{ index: number; class?: HTMLAttributes['class'] }>();

const delegatedProps = reactiveOmit(props, 'class');

const forwarded = useForwardProps(delegatedProps);

const context = useVueOTPContext();

const slot = computed(() => context?.value.slots[props.index]);
</script>

<template>
  <div
    v-bind="forwarded"
    data-slot="input-otp-slot"
    :data-active="slot?.isActive"
    :class="cn(inputOtpSlotVariants(), props.class)"
  >
    {{ slot?.char }}
    <div
      v-if="slot?.hasFakeCaret"
      class="pointer-events-none absolute inset-0 flex items-center justify-center"
    >
      <div class="h-4 w-px animate-caret-blink bg-foreground duration-1000" />
    </div>
  </div>
</template>
