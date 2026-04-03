import { router, usePage } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, onMounted, watch } from 'vue';
import { resolveNetworkFailureMessage, resolveRequestFailureMessage } from '@/lib/request-failures';
import { useToast } from '@/composables/useToast';

export const useAppToastFeed = () => {
  const page = usePage();
  const { toasts, success: pushSuccess, error: pushError, warning: pushWarning, info: pushInfo, dismissToast } = useToast();

  const flash = computed(() => page.props.flash ?? {});
  let removeHttpExceptionListener: (() => void) | undefined;
  let removeNetworkErrorListener: (() => void) | undefined;

  onMounted(() => {
    removeHttpExceptionListener = router.on('httpException', (event) => {
      const failure = resolveRequestFailureMessage(event.detail.response.status, event.detail.response.headers['x-request-id'] ?? null);

      if (failure.tone === 'warning') {
        pushWarning(failure.message, {
          duration: 6200,
        });

        return false;
      }

      pushError(failure.message, {
        duration: 6200,
      });

      return false;
    });

    removeNetworkErrorListener = router.on('networkError', () => {
      pushError(resolveNetworkFailureMessage(), {
        duration: 6200,
      });

      return false;
    });
  });

  onBeforeUnmount(() => {
    removeHttpExceptionListener?.();
    removeNetworkErrorListener?.();
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

  return {
    toasts,
    dismissToast,
  };
};
