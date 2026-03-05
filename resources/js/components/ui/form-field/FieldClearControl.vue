<script setup lang="ts">
import { CircleX } from 'lucide-vue-next';
import type { Component, HTMLAttributes } from 'vue';
import { cn } from '@/lib/utils';

const props = withDefaults(
  defineProps<{
    as?: 'button' | 'span';
    ariaLabel: string;
    class?: HTMLAttributes['class'];
    icon?: Component;
    disabled?: boolean;
  }>(),
  {
    as: 'button',
    disabled: false,
  },
);

const emits = defineEmits<{
  (event: 'clear'): void;
}>();

function clearValue(): void {
  if (props.disabled) {
    return;
  }

  emits('clear');
}
</script>

<template>
  <component
    :is="props.as"
    :type="props.as === 'button' ? 'button' : undefined"
    :role="props.as === 'button' ? undefined : 'button'"
    :tabindex="props.as === 'button' ? undefined : 0"
    :disabled="props.as === 'button' ? props.disabled : undefined"
    :aria-label="props.ariaLabel"
    :class="cn(props.class)"
    @click.stop="clearValue"
    @keydown.enter.prevent="clearValue"
    @keydown.space.prevent="clearValue"
  >
    <slot :clear="clearValue">
      <component :is="props.icon ?? CircleX" class="size-4" />
    </slot>
  </component>
</template>
