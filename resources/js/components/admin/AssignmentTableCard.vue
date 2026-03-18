<script setup lang="ts">
import Alert from '@/components/ui/alert/Alert.vue';
import AlertDescription from '@/components/ui/alert/AlertDescription.vue';
import AlertTitle from '@/components/ui/alert/AlertTitle.vue';
import Card from '@/components/ui/card/Card.vue';

withDefaults(
  defineProps<{
    description: string;
    error?: string;
    kicker?: string;
    resultsLabel?: string;
    title: string;
  }>(),
  {
    kicker: 'Relationship management',
  },
);
</script>

<template>
  <Card variant="default" class="surface-editor-panel overflow-hidden py-0">
    <div class="border-b border-border/60 px-6 py-5">
      <div
        class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between"
      >
        <div class="space-y-2">
          <p v-if="kicker" class="section-kicker">{{ kicker }}</p>
          <h2 class="text-lg font-semibold tracking-tight">{{ title }}</h2>
          <p class="text-sm leading-6 text-muted-foreground">
            {{ description }}
          </p>
        </div>

        <p
          v-if="resultsLabel"
          class="text-xs font-semibold tracking-[0.16em] text-muted-foreground uppercase"
        >
          {{ resultsLabel }}
        </p>
      </div>
    </div>

    <slot />

    <div
      v-if="error || $slots.footer"
      class="space-y-3 border-t border-border/60 px-6 py-4"
    >
      <Alert v-if="error" variant="destructive">
        <AlertTitle>Resolve this assignment issue</AlertTitle>
        <AlertDescription>
          {{ error }}
        </AlertDescription>
      </Alert>

      <slot name="footer" />
    </div>
  </Card>
</template>
