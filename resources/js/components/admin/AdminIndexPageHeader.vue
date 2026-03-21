<script setup lang="ts">
defineProps<{
  title: string;
}>();
</script>

<template>
  <div data-slot="admin-index-page-header" class="motion-step flex flex-wrap items-center justify-between gap-3">
    <h1 class="text-2xl font-semibold">{{ title }}</h1>

    <div v-if="$slots.actions" class="flex shrink-0 items-center gap-3">
      <slot name="actions" />
    </div>
  </div>
</template>
