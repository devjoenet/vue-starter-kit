<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import Card from '@/components/ui/card/Card.vue';

defineProps<{
  canSubmit: boolean;
  description: string;
  error?: string;
  processing: boolean;
  saveLabel: string;
  title: string;
}>();

defineEmits<{
  (event: 'save'): void;
}>();
</script>

<template>
  <Card variant="default" class="overflow-hidden py-0">
    <div class="flex items-center justify-between gap-3 border-b border-border/60 px-6 py-4">
      <div class="space-y-1">
        <h2 class="text-lg font-semibold">{{ title }}</h2>
        <p class="text-sm text-muted-foreground">
          {{ description }}
        </p>
      </div>

      <Button
        appearance="filled"
        :disabled="!canSubmit || processing"
        @click="$emit('save')"
      >
        {{ saveLabel }}
      </Button>
    </div>

    <slot />

    <p v-if="error" class="px-6 py-3 text-xs opacity-80">
      {{ error }}
    </p>
  </Card>
</template>
