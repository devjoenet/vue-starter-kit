<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';

defineProps<{
  canDelete: boolean;
  canSave: boolean;
  deleteLabel: string;
  processing: boolean;
  saveLabel: string;
}>();

defineEmits<{
  (event: 'delete'): void;
  (event: 'save'): void;
}>();
</script>

<template>
  <div
    class="flex flex-wrap items-center justify-end gap-3 border-t border-border/60 pt-6"
  >
    <Button
      appearance="outline"
      variant="destructive"
      :disabled="!canDelete || processing"
      @click="$emit('delete')"
    >
      {{ deleteLabel }}
    </Button>

    <Button
      appearance="filled"
      :disabled="!canSave || processing"
      @click="$emit('save')"
    >
      {{ saveLabel }}
    </Button>
  </div>
</template>
