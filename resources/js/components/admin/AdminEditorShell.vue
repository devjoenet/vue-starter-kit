<script setup lang="ts">
import { cn } from 'tailwind-variants';
import type { HTMLAttributes } from 'vue';

const props = defineProps<{
  class?: HTMLAttributes['class'];
  contentClass?: HTMLAttributes['class'];
}>();
</script>

<template>
  <section data-slot="admin-editor-shell" :class="cn('surface-editor-shell relative overflow-hidden rounded-[1.75rem] px-4 py-6 sm:px-6', props.class)">
    <div :class="cn('relative space-y-6', props.contentClass)">
      <slot />
    </div>
  </section>
</template>
