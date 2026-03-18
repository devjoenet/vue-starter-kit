<script setup lang="ts">
import {
  CheckCircle2,
  CircleAlert,
  Info,
  TriangleAlert,
  X,
} from 'lucide-vue-next';
import type { LucideIcon } from 'lucide-vue-next';
import { computed } from 'vue';
import type { ToastAppearance, ToastTone } from '@/composables/useToast';
import { cn } from '@/lib/utils';

const props = defineProps<{
  item: {
    animate: boolean;
    appearance: ToastAppearance;
    duration: number;
    icon?: unknown;
    id: number;
    message: string;
    title?: string;
    tone: ToastTone;
  };
}>();

defineEmits<{
  (event: 'dismiss', id: number): void;
}>();

const toneIconMap: Record<ToastTone, LucideIcon> = {
  default: Info,
  success: CheckCircle2,
  error: CircleAlert,
  warning: TriangleAlert,
  info: Info,
};

const toneClasses: Record<ToastTone, string> = {
  default: 'border-border/80 text-foreground',
  success: 'border-success/35 text-foreground',
  error: 'border-destructive/35 text-foreground',
  warning: 'border-warning/40 text-foreground',
  info: 'border-info/40 text-foreground',
};

const appearanceClasses: Record<ToastAppearance, string> = {
  default: 'bg-card/98 shadow-[var(--elevation-2)]',
  outline: 'bg-background/95 shadow-[var(--elevation-1)]',
  solid: 'shadow-[var(--elevation-2)]',
};

const solidToneClasses: Record<ToastTone, string> = {
  default: 'bg-[var(--foreground)] text-[var(--background)] border-transparent',
  success: 'bg-success text-success-foreground border-transparent',
  error: 'bg-destructive text-destructive-foreground border-transparent',
  warning: 'bg-warning text-warning-foreground border-transparent',
  info: 'bg-info text-info-foreground border-transparent',
};

const progressClasses: Record<ToastTone, string> = {
  default: 'bg-[var(--foreground)]/35',
  success: 'bg-success',
  error: 'bg-destructive',
  warning: 'bg-warning',
  info: 'bg-info',
};

const toneBadgeClasses: Record<ToastTone, string> = {
  default: 'bg-muted text-foreground',
  success: 'bg-success/18 text-success',
  error: 'bg-destructive/16 text-destructive',
  warning: 'bg-warning/18 text-warning',
  info: 'bg-info/16 text-info',
};

const toneLabelMap: Record<ToastTone, string> = {
  default: 'Update',
  success: 'Success',
  error: 'Error',
  warning: 'Warning',
  info: 'Info',
};

const surfaceProgressClasses: Record<ToastAppearance, string> = {
  default: 'bg-border/70',
  outline: 'bg-border/60',
  solid: 'bg-background/25',
};

const resolvedIcon = computed<LucideIcon | null>(() => {
  if (props.item.icon === null) {
    return null;
  }

  return (
    (props.item.icon as LucideIcon | undefined) ?? toneIconMap[props.item.tone]
  );
});

const toastClasses = computed(() => {
  if (props.item.appearance === 'solid') {
    return cn(
      'pointer-events-auto relative w-72 overflow-hidden rounded-[1.375rem] border pt-4 pr-4 pb-4 pl-4',
      appearanceClasses.solid,
      solidToneClasses[props.item.tone],
    );
  }

  return cn(
    'pointer-events-auto relative w-72 overflow-hidden rounded-[1.375rem] border pt-4 pr-4 pb-4 pl-4',
    toneClasses[props.item.tone],
    appearanceClasses[props.item.appearance],
  );
});
</script>

<template>
  <div :class="toastClasses" role="status" aria-live="polite">
    <button
      type="button"
      class="absolute top-2 right-2 flex size-10 items-center justify-center rounded-full opacity-70 transition hover:opacity-100"
      @click="$emit('dismiss', item.id)"
    >
      <X class="size-4" />
    </button>

    <div class="flex items-start gap-3 pr-8">
      <span
        v-if="resolvedIcon"
        :class="
          cn(
            'mt-0.5 flex size-8 shrink-0 items-center justify-center rounded-full',
            toneBadgeClasses[item.tone],
          )
        "
      >
        <component :is="resolvedIcon" class="size-4" />
      </span>

      <div class="min-w-0 pr-2">
        <p
          class="text-[0.68rem] font-semibold tracking-[0.16em] uppercase"
          :class="toneBadgeClasses[item.tone]"
        >
          {{ toneLabelMap[item.tone] }}
        </p>
        <p v-if="item.title" class="mt-1 text-sm font-semibold">
          {{ item.title }}
        </p>
        <p
          class="mt-1 text-sm leading-6"
          :class="item.title ? 'opacity-95' : 'font-medium'"
        >
          {{ item.message }}
        </p>
      </div>
    </div>

    <div
      class="absolute inset-x-0 bottom-0 h-1"
      :class="surfaceProgressClasses[item.appearance]"
    >
      <div
        class="h-full origin-left transition-transform ease-linear motion-reduce:transition-none"
        :class="progressClasses[item.tone]"
        :style="{
          transform: item.animate ? 'scaleX(0)' : 'scaleX(1)',
          transitionDuration: `${item.duration}ms`,
        }"
      />
    </div>
  </div>
</template>
