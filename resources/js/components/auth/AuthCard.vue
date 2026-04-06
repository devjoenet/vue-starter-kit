<script setup lang="ts">
import { cn } from 'tailwind-variants';
import type { HTMLAttributes } from 'vue';
import Card from '@/components/ui/card/Card.vue';

const props = defineProps<{
  class?: HTMLAttributes['class'];
}>();
</script>

<template>
  <Card appearance="glow" variant="secondary" :class="cn('gap-5 rounded-3xl px-5 py-5 sm:px-6 sm:py-6', props.class)">
    <slot />
  </Card>
</template>
