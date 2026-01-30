<script setup lang="ts">
  import type { HTMLAttributes } from "vue";

  defineOptions({
    inheritAttrs: false,
  });

  type Props = {
    className?: HTMLAttributes["class"];
  };

  const props = defineProps<Props>();
</script>

<template>
  <svg :class="props.className" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" aria-label="Southeast Code">
    <rect x="6" y="6" width="52" height="52" rx="14" fill="var(--md-surface)" />
    <path d="M23 22c0-3 2.4-5 6-5h10c3.6 0 6 2 6 5s-2.4 5-6 5H29c-3.6 0-6 2-6 5s2.4 5 6 5h10" stroke="var(--sc-primary)" stroke-width="4" stroke-linecap="round" />
    <path d="M24 38l-6-6 6-6" stroke="var(--sc-secondary)" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" />
    <path d="M40 26l6 6-6 6" stroke="var(--sc-accent)" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" />
  </svg>
</template>
