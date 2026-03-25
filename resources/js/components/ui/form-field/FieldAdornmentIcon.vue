<script setup lang="ts">
import type { Component, HTMLAttributes } from 'vue';
import { cn } from 'tailwind-variants';

const props = withDefaults(
  defineProps<{
    show?: boolean;
    icon?: Component | string;
    class?: HTMLAttributes['class'];
    iconClass?: HTMLAttributes['class'];
    textClass?: HTMLAttributes['class'];
  }>(),
  {
    show: true,
    iconClass: 'size-5',
    textClass: 'text-sm font-medium',
  },
);
</script>

<template>
  <span v-if="props.show" :class="cn(props.class)">
    <slot>
      <component :is="props.icon" v-if="props.icon && typeof props.icon !== 'string'" :class="props.iconClass" />
      <span v-else-if="props.icon" :class="props.textClass">
        {{ props.icon }}
      </span>
    </slot>
  </span>
</template>
