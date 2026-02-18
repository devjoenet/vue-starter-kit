<script setup lang="ts">
  import { router, usePage } from "@inertiajs/vue3";
  import { CheckCircle2, CircleAlert, Info, TriangleAlert, X, type LucideIcon } from "lucide-vue-next";
  import { computed, onBeforeUnmount, onMounted, watch } from "vue";
  import { useToast, type ToastAppearance, type ToastItem, type ToastTone } from "@/composables/useToast";
  import { cn } from "@/lib/utils";

  const page = usePage();
  const { toasts, success: pushSuccess, error: pushError, warning: pushWarning, info: pushInfo, dismissToast } = useToast();

  const flash = computed(() => page.props.flash ?? {});

  const normalizeMessages = (errors: unknown): string[] => {
    if (!errors || typeof errors !== "object") {
      return [];
    }

    const values = Object.values(errors as Record<string, unknown>);

    return values
      .flatMap((value) => {
        if (Array.isArray(value)) {
          return value.map((item) => String(item));
        }

        if (typeof value === "string") {
          return [value];
        }

        return [];
      })
      .filter((message, index, list) => message && list.indexOf(message) === index);
  };

  const toneIconMap: Record<ToastTone, LucideIcon> = {
    default: Info,
    success: CheckCircle2,
    error: CircleAlert,
    warning: TriangleAlert,
    info: Info,
  };

  const resolveIcon = (toastItem: ToastItem): LucideIcon | null => {
    if (toastItem.icon === null) {
      return null;
    }

    return (toastItem.icon as LucideIcon | undefined) ?? toneIconMap[toastItem.tone];
  };

  const toneClasses: Record<ToastTone, string> = {
    default: "border-[color:var(--outline)] text-foreground",
    success: "border-success/35 text-foreground",
    error: "border-[var(--error)]/35 text-foreground",
    warning: "border-warning/40 text-foreground",
    info: "border-info/40 text-foreground",
  };

  const appearanceClasses: Record<ToastAppearance, string> = {
    default: "bg-[var(--surface)]/94 backdrop-blur-xl shadow-[var(--elevation-2)]",
    glass: "liquid-glass liquid-glass-hover border-transparent",
    outline: "bg-[var(--surface)]/40 backdrop-blur-xl",
    solid: "text-white shadow-[var(--elevation-2)]",
  };

  const solidToneClasses: Record<ToastTone, string> = {
    default: "bg-[var(--foreground)] text-[var(--background)] border-transparent",
    success: "bg-success border-transparent",
    error: "bg-[var(--error)] border-transparent",
    warning: "bg-warning border-transparent",
    info: "bg-info border-transparent",
  };

  const progressClasses: Record<ToastTone, string> = {
    default: "bg-[var(--foreground)]/35",
    success: "bg-success",
    error: "bg-[var(--error)]",
    warning: "bg-warning",
    info: "bg-info",
  };

  const surfaceProgressClasses: Record<ToastAppearance, string> = {
    default: "bg-black/8 dark:bg-white/10",
    glass: "bg-black/10 dark:bg-white/14",
    outline: "bg-black/12 dark:bg-white/12",
    solid: "bg-white/25",
  };

  const toastClasses = (toastItem: ToastItem): string => {
    if (toastItem.appearance === "solid") {
      return cn("pointer-events-auto relative overflow-hidden rounded-2xl border px-4 py-3", appearanceClasses.solid, solidToneClasses[toastItem.tone]);
    }

    return cn("pointer-events-auto relative overflow-hidden rounded-2xl border px-4 py-3", toneClasses[toastItem.tone], appearanceClasses[toastItem.appearance]);
  };

  let removeErrorListener: (() => void) | undefined;

  onMounted(() => {
    removeErrorListener = router.on("error", (errors) => {
      const messages = normalizeMessages(errors);
      if (messages.length === 0) {
        pushError("Unable to complete the request.", {
          duration: 5200,
        });
        return;
      }

      pushError(messages[0], {
        duration: 5200,
      });
    });
  });

  onBeforeUnmount(() => {
    removeErrorListener?.();
  });

  watch(
    () => [flash.value.success, flash.value.error, flash.value.warning, flash.value.info] as const,
    ([success, error, warning, info], previousValue) => {
      const [previousSuccess, previousError, previousWarning, previousInfo] = previousValue ?? [undefined, undefined, undefined, undefined];

      if (success && success !== previousSuccess) {
        pushSuccess(success);
      }

      if (error && error !== previousError) {
        pushError(error, {
          duration: 5200,
        });
      }

      if (warning && warning !== previousWarning) {
        pushWarning(warning, {
          duration: 5200,
        });
      }

      if (info && info !== previousInfo) {
        pushInfo(info);
      }
    },
    { immediate: true },
  );
</script>

<template>
  <Teleport to="body">
    <div class="pointer-events-none fixed right-4 top-4 z-[200] flex w-[min(94vw,24rem)] flex-col gap-2">
      <TransitionGroup name="toast" tag="div" class="space-y-2">
        <div v-for="item in toasts" :key="item.id" :class="toastClasses(item)" role="status" aria-live="polite">
          <button type="button" class="absolute right-2 top-2 rounded p-1 opacity-70 transition hover:opacity-100" @click="dismissToast(item.id)">
            <X class="size-4" />
          </button>

          <div class="flex items-start gap-2.5 pr-8">
            <component :is="resolveIcon(item)" v-if="resolveIcon(item)" class="mt-0.5 size-4 shrink-0" />

            <div class="min-w-0 space-y-0.5">
              <p v-if="item.title" class="text-sm font-semibold">{{ item.title }}</p>
              <p class="text-sm" :class="item.title ? 'opacity-95' : 'font-medium'">{{ item.message }}</p>
            </div>
          </div>

          <div class="absolute inset-x-0 bottom-0 h-1" :class="surfaceProgressClasses[item.appearance]">
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
      </TransitionGroup>
    </div>
  </Teleport>
</template>

<style scoped>
  .toast-enter-active,
  .toast-leave-active {
    transition: all 180ms ease;
  }

  .toast-enter-from,
  .toast-leave-to {
    opacity: 0;
    transform: translateY(-8px) scale(0.98);
  }
</style>
