<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import Button from '@/components/ui/button/Button.vue';

const props = withDefaults(
  defineProps<{
    canDelete?: boolean;
    canSave?: boolean;
    class?: HTMLAttributes['class'];
    closeId?: string;
    closeLabel?: string;
    deleteId?: string;
    deleteLabel?: string;
    description?: string;
    heading?: string;
    processing: boolean;
    saveId?: string;
    saveLabel?: string;
    status?: string;
    statusTone?: 'destructive' | 'info' | 'muted' | 'success' | 'warning';
  }>(),
  {
    canDelete: false,
    canSave: true,
    closeLabel: 'Close',
    deleteLabel: 'Delete',
    heading: 'Finalize changes',
    description: 'Review the current state, then save and return to the index when you are ready.',
    saveLabel: 'Save and Close',
    statusTone: 'muted',
  },
);

defineEmits<{
  (event: 'close'): void;
  (event: 'delete'): void;
  (event: 'save'): void;
}>();

const statusClassNames: Record<NonNullable<typeof props.statusTone>, string> = {
  muted: 'text-muted-foreground',
  success: 'text-success',
  info: 'text-info',
  warning: 'text-warning',
  destructive: 'text-destructive',
};
</script>

<template>
  <div :class="['surface-editor-action-zone flex flex-col gap-5 rounded-[1.5rem] px-5 py-5', props.class]">
    <div class="space-y-2">
      <p class="section-kicker">Review and finish</p>
      <div class="space-y-2">
        <h2 class="text-lg font-semibold tracking-tight">{{ heading }}</h2>
        <p class="text-sm leading-6 text-muted-foreground">
          {{ description }}
        </p>
      </div>

      <p v-if="status" :class="['text-sm font-medium', statusClassNames[statusTone]]">
        {{ status }}
      </p>
    </div>

    <div class="flex flex-col gap-3">
      <div class="flex flex-col gap-2 sm:flex-row">
        <Button appearance="outline" type="button" variant="muted" class="min-h-11 flex-1" :id="closeId" :disabled="processing" @click="$emit('close')">
          {{ closeLabel }}
        </Button>

        <Button v-if="canSave" appearance="filled" type="button" class="min-h-11 flex-1" :id="saveId" :disabled="processing" @click="$emit('save')">
          {{ saveLabel }}
        </Button>
      </div>

      <Button v-if="canDelete" appearance="outline" type="button" variant="destructive" class="min-h-11 justify-start" :id="deleteId" :disabled="processing" @click="$emit('delete')">
        {{ deleteLabel }}
      </Button>
    </div>
  </div>
</template>
