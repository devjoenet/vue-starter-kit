<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

const props = withDefaults(
  defineProps<{
    label?: string;
    forId?: string;
    class?: HTMLAttributes['class'];
    showAsterisk?: boolean;
    asteriskClass?: HTMLAttributes['class'];
  }>(),
  {
    showAsterisk: false,
    asteriskClass: 'text-[var(--error)]',
  },
);
</script>

<template>
  <label v-if="props.label" :for="props.forId" :class="props.class">
    {{ props.label }}
    <span v-if="props.showAsterisk" :class="props.asteriskClass"> *</span>
  </label>
</template>
