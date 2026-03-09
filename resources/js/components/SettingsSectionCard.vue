<script setup lang="ts">
import type { CardVariants } from '@/components/ui/card/styles';
import Card from '@/components/ui/card/Card.vue';
import Heading from '@/components/Heading.vue';

withDefaults(
  defineProps<{
    description?: string;
    title: string;
    variant?: CardVariants['variant'];
  }>(),
  {
    variant: 'default',
  },
);
</script>

<template>
  <Card :variant="variant" class="px-6">
    <div class="space-y-4">
      <Heading variant="small" :title="title" :description="description" />

      <slot />
    </div>
  </Card>
</template>
