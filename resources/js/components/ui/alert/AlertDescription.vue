<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import { cn } from 'tailwind-variants';
import { alertDescription } from './variants';

const props = defineProps<{
  class?: HTMLAttributes['class'];
}>();
</script>

<template>
  <div data-slot="alert-description" :class="cn(alertDescription, props.class)">
    <slot />
  </div>
</template>
