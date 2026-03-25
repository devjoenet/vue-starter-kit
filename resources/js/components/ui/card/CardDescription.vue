<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import { cn } from 'tailwind-variants';
import { cardDescriptionVariants } from './variants';

const props = defineProps<{
  class?: HTMLAttributes['class'];
}>();
</script>

<template>
  <p data-slot="card-description" :class="cn(cardDescriptionVariants(), props.class)">
    <slot />
  </p>
</template>
