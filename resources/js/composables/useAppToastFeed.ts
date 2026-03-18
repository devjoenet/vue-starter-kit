import { router, usePage } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, onMounted, watch } from 'vue';
import { useToast } from '@/composables/useToast';

export const useAppToastFeed = () => {
  const page = usePage();
  const { toasts, success: pushSuccess, error: pushError, warning: pushWarning, info: pushInfo, dismissToast } = useToast();

  const flash = computed(() => page.props.flash ?? {});
  let removeHttpExceptionListener: (() => void) | undefined;
  let removeNetworkErrorListener: (() => void) | undefined;

  onMounted(() => {
    removeHttpExceptionListener = router.on('httpException', (event) => {
      pushError(event.detail.response.status >= 500 ? 'The server was unable to complete the request.' : 'Unable to complete the request.', {
        duration: 5200,
      });
    });

    removeNetworkErrorListener = router.on('networkError', () => {
      pushError('Unable to reach the server.', {
        duration: 5200,
      });
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
