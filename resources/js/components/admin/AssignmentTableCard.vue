<script setup lang="ts">
import Card from '@/components/ui/card/Card.vue';

defineProps<{
  description: string;
  error?: string;
  title: string;
}>();
</script>

<template>
  <Card variant="default" class="overflow-hidden py-0">
    <div class="border-b border-border/60 px-6 py-4">
      <div class="space-y-1">
        <h2 class="text-lg font-semibold">{{ title }}</h2>
        <p class="text-sm text-muted-foreground">
          {{ description }}
        </p>
      </div>
    </div>

    <slot />

    <p v-if="error" class="px-6 py-3 text-xs opacity-80">
      {{ error }}
    </p>
  </Card>
</template>
