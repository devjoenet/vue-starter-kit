import {
  defaultDocument,
  defaultWindow,
  usePreferredDark,
  useStorage,
} from '@vueuse/core';
import type { ComputedRef, Ref } from 'vue';
import { computed, watch } from 'vue';
import type { Appearance, ResolvedAppearance } from '@/types/ui';

export type { Appearance, ResolvedAppearance };

export type UseAppearanceReturn = {
  appearance: Ref<Appearance>;
  resolvedAppearance: ComputedRef<ResolvedAppearance>;
  updateAppearance: (value: Appearance) => void;
};

const APPEARANCE_STORAGE_KEY = 'appearance';
const APPEARANCE_COOKIE_NAME = 'appearance';
const APPEARANCE_COOKIE_MAX_AGE = 365 * 24 * 60 * 60;

const appearanceStorage = useStorage<Appearance>(
  APPEARANCE_STORAGE_KEY,
  'system',
  defaultWindow?.localStorage,
);

const prefersDark = usePreferredDark({ window: defaultWindow });

const resolvedAppearance = computed<ResolvedAppearance>(() => {
  if (appearanceStorage.value === 'system') {
    return prefersDark.value ? 'dark' : 'light';
  }

  return appearanceStorage.value;
});

function setAppearanceCookie(value: Appearance): void {
  if (!defaultDocument) {
    return;
  }

  defaultDocument.cookie = `${APPEARANCE_COOKIE_NAME}=${value};path=/;max-age=${APPEARANCE_COOKIE_MAX_AGE};SameSite=Lax`;
}

export function updateTheme(value: Appearance): void {
  if (!defaultDocument) {
    return;
  }

  const nextResolvedAppearance =
    value === 'system' ? (prefersDark.value ? 'dark' : 'light') : value;

  defaultDocument.documentElement.classList.toggle(
    'dark',
    nextResolvedAppearance === 'dark',
  );
}

function syncAppearance(): void {
  updateTheme(appearanceStorage.value);
  setAppearanceCookie(appearanceStorage.value);
}

watch([appearanceStorage, resolvedAppearance], syncAppearance, {
  immediate: true,
});

export function initializeTheme(): void {
  syncAppearance();
}

export function useAppearance(): UseAppearanceReturn {
  function updateAppearance(value: Appearance): void {
    appearanceStorage.value = value;
  }

  return {
    appearance: appearanceStorage as Ref<Appearance>,
    resolvedAppearance,
    updateAppearance,
  };
}
