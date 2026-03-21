<script setup lang="ts">
import Card from '@/components/ui/card/Card.vue';

defineProps<{
  description: string;
  kicker: string;
}>();
</script>

<template>
  <Card variant="default" class="motion-step gap-4 px-4 py-4 md:hidden">
    <div class="space-y-1.5">
      <p class="section-kicker">{{ kicker }}</p>
      <p class="text-sm leading-6 text-muted-foreground">
        {{ description }}
      </p>
    </div>

    <div class="grid gap-3">
      <slot />
    </div>
  </Card>
</template>
