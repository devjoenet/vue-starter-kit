<script setup lang="ts">
import {
  CheckCircle2,
  CircleAlert,
  Info,
  TriangleAlert,
  X,
  type LucideIcon,
} from 'lucide-vue-next';
import { computed } from 'vue';
import { cn } from '@/lib/utils';
import type { ToastAppearance, ToastTone } from '@/composables/useToast';

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
  default: 'border-[color:var(--outline)] text-foreground',
  success: 'border-success/35 text-foreground',
  error: 'border-[var(--error)]/35 text-foreground',
  warning: 'border-warning/40 text-foreground',
  info: 'border-info/40 text-foreground',
};

const appearanceClasses: Record<ToastAppearance, string> = {
  default:
    'bg-[var(--surface)]/94 backdrop-blur-xl shadow-[var(--elevation-2)]',
  glass: 'liquid-glass liquid-glass-hover border-transparent',
  outline: 'bg-[var(--surface)]/40 backdrop-blur-xl',
  solid: 'text-white shadow-[var(--elevation-2)]',
};

const solidToneClasses: Record<ToastTone, string> = {
  default: 'bg-[var(--foreground)] text-[var(--background)] border-transparent',
  success: 'bg-success border-transparent',
  error: 'bg-[var(--error)] border-transparent',
  warning: 'bg-warning border-transparent',
  info: 'bg-info border-transparent',
};

const progressClasses: Record<ToastTone, string> = {
  default: 'bg-[var(--foreground)]/35',
  success: 'bg-success',
  error: 'bg-[var(--error)]',
  warning: 'bg-warning',
  info: 'bg-info',
};

const surfaceProgressClasses: Record<ToastAppearance, string> = {
  default: 'bg-black/8 dark:bg-white/10',
  glass: 'bg-black/10 dark:bg-white/14',
  outline: 'bg-black/12 dark:bg-white/12',
  solid: 'bg-white/25',
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
      'pointer-events-auto relative overflow-hidden rounded-2xl border px-4 py-3',
      appearanceClasses.solid,
      solidToneClasses[props.item.tone],
    );
  }

  return cn(
    'pointer-events-auto relative overflow-hidden rounded-2xl border px-4 py-3',
    toneClasses[props.item.tone],
    appearanceClasses[props.item.appearance],
  );
});
</script>

<template>
  <div :class="toastClasses" role="status" aria-live="polite">
    <button
      type="button"
      class="absolute top-2 right-2 rounded p-1 opacity-70 transition hover:opacity-100"
      @click="$emit('dismiss', item.id)"
    >
      <X class="size-4" />
    </button>

    <div class="flex items-start gap-2.5 pr-8">
      <component
        :is="resolvedIcon"
        v-if="resolvedIcon"
        class="mt-0.5 size-4 shrink-0"
      />

      <div class="min-w-0 space-y-0.5">
        <p v-if="item.title" class="text-sm font-semibold">
          {{ item.title }}
        </p>
        <p class="text-sm" :class="item.title ? 'opacity-95' : 'font-medium'">
          {{ item.message }}
        </p>
      </div>
    </div>

    <div
      class="absolute inset-x-0 bottom-0 h-1"
      :class="surfaceProgressClasses[item.appearance]"
    >
      <div
        class="h-full transition-[width] ease-linear"
        :class="progressClasses[item.tone]"
        :style="{
          width: item.animate ? '0%' : '100%',
          transitionDuration: `${item.duration}ms`,
        }"
      />
    </div>
  </div>
</template>
