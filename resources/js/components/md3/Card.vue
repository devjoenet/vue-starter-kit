<script setup lang="ts">
withDefaults(defineProps<{
  elevation?: 0 | 1 | 2 | 3
}>(), { elevation: 1 })
</script>

<template>
  <div :class="['md3-card', `md3-card--e${elevation}`]">
    <slot />
  </div>
</template>
