import type { MaybeRefOrGetter, Ref } from 'vue';
import { toValue, watch } from 'vue';

type UseDisclosureTriggerOptions = {
  open: Ref<boolean>;
  disabled: MaybeRefOrGetter<boolean>;
  readonly: MaybeRefOrGetter<boolean>;
  onOpenChange?: (isOpen: boolean) => void;
  closeWhenDisabledOrReadonly?: boolean;
};

export function useDisclosureTrigger(options: UseDisclosureTriggerOptions): {
  handleTriggerKeydown: (event: KeyboardEvent) => void;
} {
  if (options.closeWhenDisabledOrReadonly) {
    watch(
      () => [toValue(options.disabled), toValue(options.readonly)],
      ([disabled, readonly]) => {
        if (disabled || readonly) {
          options.open.value = false;
        }
      },
    );
  }

  if (options.onOpenChange) {
    watch(options.open, (value) => {
      options.onOpenChange?.(value);
    });
  }

  function handleTriggerKeydown(event: KeyboardEvent): void {
    if (toValue(options.disabled) || toValue(options.readonly)) {
      return;
    }

    if (event.key === 'Enter' || event.key === ' ') {
      event.preventDefault();
      options.open.value = !options.open.value;
      return;
    }

    if (event.key === 'ArrowDown') {
      event.preventDefault();
      options.open.value = true;
      return;
    }

    if (event.key === 'Escape' && options.open.value) {
      event.preventDefault();
      options.open.value = false;
    }
  }

  return {
    handleTriggerKeydown,
  };
}
