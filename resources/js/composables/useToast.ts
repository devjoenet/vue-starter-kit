import type { Component, DeepReadonly, Ref } from "vue";
import { readonly, ref } from "vue";

export type ToastTone = "default" | "success" | "error" | "warning" | "info";
export type ToastAppearance = "default" | "glass" | "outline" | "solid";

export type ToastPayload = {
  message: string;
  title?: string;
  tone?: ToastTone;
  appearance?: ToastAppearance;
  duration?: number;
  icon?: Component | null;
};

export type ToastItem = {
  id: number;
  message: string;
  title?: string;
  tone: ToastTone;
  appearance: ToastAppearance;
  duration: number;
  icon?: Component | null;
  animate: boolean;
};

export type UseToastReturn = {
  toasts: DeepReadonly<Ref<ToastItem[]>>;
  pushToast: (payload: string | ToastPayload) => number | null;
  dismissToast: (id: number) => void;
  clearToasts: () => void;
  success: (message: string, options?: Omit<ToastPayload, "message" | "tone">) => number | null;
  error: (message: string, options?: Omit<ToastPayload, "message" | "tone">) => number | null;
  info: (message: string, options?: Omit<ToastPayload, "message" | "tone">) => number | null;
  warning: (message: string, options?: Omit<ToastPayload, "message" | "tone">) => number | null;
};

const DEFAULT_DURATION = 4200;
const MAX_TOASTS = 4;

const toasts = ref<ToastItem[]>([]);
const timeoutIds = new Map<number, ReturnType<typeof setTimeout>>();
let nextToastId = 1;

const normalizePayload = (payload: string | ToastPayload): ToastPayload => {
  if (typeof payload === "string") {
    return {
      message: payload,
    };
  }

  return payload;
};

const dismissToast = (id: number): void => {
  const timeoutId = timeoutIds.get(id);
  if (timeoutId) {
    clearTimeout(timeoutId);
    timeoutIds.delete(id);
  }

  toasts.value = toasts.value.filter((toast) => toast.id !== id);
};

const queueToast = (payload: string | ToastPayload): number | null => {
  const normalizedPayload = normalizePayload(payload);

  if (!normalizedPayload.message.trim()) {
    return null;
  }

  const toast: ToastItem = {
    id: nextToastId++,
    title: normalizedPayload.title,
    message: normalizedPayload.message,
    tone: normalizedPayload.tone ?? "default",
    appearance: normalizedPayload.appearance ?? "default",
    duration: normalizedPayload.duration ?? DEFAULT_DURATION,
    icon: normalizedPayload.icon,
    animate: false,
  };

  const currentToasts = [...toasts.value, toast];

  if (currentToasts.length > MAX_TOASTS) {
    const removedToast = currentToasts.shift();
    if (removedToast) {
      dismissToast(removedToast.id);
    }
  }

  toasts.value = currentToasts;

  requestAnimationFrame(() => {
    const targetToast = toasts.value.find((item) => item.id === toast.id);
    if (!targetToast) {
      return;
    }

    targetToast.animate = true;
  });

  const timeoutId = setTimeout(() => {
    dismissToast(toast.id);
  }, toast.duration);

  timeoutIds.set(toast.id, timeoutId);

  return toast.id;
};

const clearToasts = (): void => {
  timeoutIds.forEach((timeoutId) => clearTimeout(timeoutId));
  timeoutIds.clear();
  toasts.value = [];
};

export function useToast(): UseToastReturn {
  return {
    toasts: readonly(toasts),
    pushToast: queueToast,
    dismissToast,
    clearToasts,
    success: (message, options) => {
      return queueToast({ message, tone: "success", ...options });
    },
    error: (message, options) => {
      return queueToast({ message, tone: "error", ...options });
    },
    info: (message, options) => {
      return queueToast({ message, tone: "info", ...options });
    },
    warning: (message, options) => {
      return queueToast({ message, tone: "warning", ...options });
    },
  };
}
