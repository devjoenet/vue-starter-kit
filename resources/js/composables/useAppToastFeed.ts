import { router, usePage } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, onMounted, watch } from 'vue';
import { useToast } from '@/composables/useToast';

const normalizeMessages = (errors: unknown): string[] => {
  if (!errors || typeof errors !== 'object') {
    return [];
  }

  const values = Object.values(errors as Record<string, unknown>);

  return values
    .flatMap((value) => {
      if (Array.isArray(value)) {
        return value.map((item) => String(item));
      }

      if (typeof value === 'string') {
        return [value];
      }

      return [];
    })
    .filter(
      (message, index, list) => message && list.indexOf(message) === index,
    );
};

export const useAppToastFeed = () => {
  const page = usePage();
  const {
    toasts,
    success: pushSuccess,
    error: pushError,
    warning: pushWarning,
    info: pushInfo,
    dismissToast,
  } = useToast();

  const flash = computed(() => page.props.flash ?? {});
  let removeErrorListener: (() => void) | undefined;

  onMounted(() => {
    removeErrorListener = router.on('error', (errors) => {
      const messages = normalizeMessages(errors);
      if (messages.length === 0) {
        pushError('Unable to complete the request.', {
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
    () =>
      [
        flash.value.success,
        flash.value.error,
        flash.value.warning,
        flash.value.info,
      ] as const,
    ([success, error, warning, info], previousValue) => {
      const [previousSuccess, previousError, previousWarning, previousInfo] =
        previousValue ?? [undefined, undefined, undefined, undefined];

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

  return {
    toasts,
    dismissToast,
  };
};
