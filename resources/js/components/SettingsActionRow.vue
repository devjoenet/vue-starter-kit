<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import { computed, useSlots } from 'vue';
import { cn } from '@/lib/utils';

const props = withDefaults(
  defineProps<{
    align?: 'auto' | 'between' | 'end';
    class?: HTMLAttributes['class'];
    status?: string;
    statusTone?: 'muted' | 'success' | 'destructive';
  }>(),
  {
    align: 'auto',
    statusTone: 'muted',
  },
);

const slots = useSlots();

const hasMetaContent = computed(() => Boolean(props.status || slots.meta));

const alignmentClass = computed(() => {
  if (props.align === 'between') {
    return 'sm:justify-between';
  }

  if (props.align === 'end') {
    return 'sm:justify-end';
  }

  return hasMetaContent.value ? 'sm:justify-between' : 'sm:justify-end';
});

const statusClassNames: Record<'muted' | 'success' | 'destructive', string> = {
  muted: 'text-muted-foreground',
  success: 'text-success',
  destructive: 'text-destructive',
};
</script>

<template>
  <div
    data-slot="settings-action-row"
    :class="
      cn(
        'flex flex-col gap-3 sm:flex-row sm:items-center',
        alignmentClass,
        props.class,
      )
    "
  >
    <div v-if="hasMetaContent" class="space-y-1">
      <p v-if="status" :class="cn('text-sm', statusClassNames[statusTone])">
        {{ status }}
      </p>

      <slot name="meta" />
    </div>

    <div class="flex flex-col-reverse gap-2 sm:flex-row sm:items-center">
      <slot />
    </div>
  </div>
</template>
