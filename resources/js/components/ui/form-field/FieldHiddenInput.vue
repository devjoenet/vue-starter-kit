<script setup lang="ts">
const props = defineProps<{
  name?: string;
  value?: string | number | null;
}>();
</script>

<template>
  <input v-if="props.name" type="hidden" :name="props.name" :value="props.value ?? ''" />
</template>
