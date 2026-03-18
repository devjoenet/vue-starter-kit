import type { ComputedRef, MaybeRefOrGetter } from 'vue';
import { computed, toValue } from 'vue';
import type { InputVariants } from '@/components/ui/input/variants';

type FieldState = NonNullable<InputVariants['state']>;

export type FieldStateSource = {
  state?: InputVariants['state'];
  error?: boolean | string;
  errorText?: string;
  message?: string;
  supportingText?: string;
  required?: boolean;
  noAsterisk?: boolean;
};

export function resolveHasError(source: FieldStateSource): boolean {
  if (source.state === 'error' || source.state === 'destructive') {
    return true;
  }

  if (typeof source.error === 'string') {
    return true;
  }

  return Boolean(source.error);
}

export function resolveFieldState(source: FieldStateSource): FieldState {
  if (resolveHasError(source)) {
    if (source.state === 'destructive') {
      return 'destructive';
    }

    return 'error';
  }

  if (source.state === 'info') {
    return 'info';
  }

  if (source.state === 'warning') {
    return 'warning';
  }

  if (source.state === 'success') {
    return 'success';
  }

  return 'default';
}

export function resolveSupportingText(source: FieldStateSource): string | undefined {
  if (resolveHasError(source)) {
    if (typeof source.error === 'string') {
      return source.error;
    }

    return source.errorText ?? source.message ?? source.supportingText;
  }

  return source.supportingText ?? source.message;
}

export function resolveShowAsterisk(source: FieldStateSource): boolean {
  return Boolean(source.required && !source.noAsterisk);
}

export function useFieldState(source: MaybeRefOrGetter<FieldStateSource>): {
  hasError: ComputedRef<boolean>;
  fieldState: ComputedRef<FieldState>;
  supportingText: ComputedRef<string | undefined>;
  showAsterisk: ComputedRef<boolean>;
} {
  const hasError = computed(() => resolveHasError(toValue(source)));
  const fieldState = computed(() => resolveFieldState(toValue(source)));
  const supportingText = computed(() => resolveSupportingText(toValue(source)));
  const showAsterisk = computed(() => resolveShowAsterisk(toValue(source)));

  return {
    hasError,
    fieldState,
    supportingText,
    showAsterisk,
  };
}
