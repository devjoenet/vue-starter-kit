<script setup lang="ts">
import Card from '@/components/ui/card/Card.vue';
</script>

<template>
  <Card variant="default" class="motion-step hidden overflow-hidden py-0 md:block">
    <slot />
  </Card>
</template>
