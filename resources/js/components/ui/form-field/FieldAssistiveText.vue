<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

const props = defineProps<{
  id?: string;
  text?: string;
  class?: HTMLAttributes['class'];
}>();
</script>

<template>
  <p v-if="props.text" :id="props.id" :class="props.class">
    {{ props.text }}
  </p>
</template>
