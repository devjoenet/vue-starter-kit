<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

const props = defineProps<{
  text?: string;
  class?: HTMLAttributes['class'];
}>();
</script>

<template>
  <p v-if="props.text" :class="props.class">
    {{ props.text }}
  </p>
</template>
