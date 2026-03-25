<script setup lang="ts">
import { cn } from 'tailwind-variants';
import { computed } from 'vue';
import { inputAssistiveTextVariants } from '@/components/ui/input/variants';
import { normalizeErrorMessages } from '@/lib/errors';

const props = defineProps<{
  message?: unknown;
  class?: string;
}>();

const messages = computed(() => normalizeErrorMessages(props.message));
</script>

<template>
  <div v-if="messages.length">
    <p v-for="(message, index) in messages" :key="`${index}-${message}`" :class="cn(inputAssistiveTextVariants({ state: 'destructive' }), props.class)">
      {{ message }}
    </p>
  </div>
</template>
