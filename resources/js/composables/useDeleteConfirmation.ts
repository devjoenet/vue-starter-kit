import { readonly, ref } from 'vue';

type DeleteConfirmationState = {
  confirmLabel: string;
  message: string;
  onConfirm: (() => void) | null;
  open: boolean;
  title: string;
};

type DeleteConfirmationOptions = {
  confirmLabel?: string;
  enabled?: boolean;
  message: string;
  onConfirm: () => void;
  title?: string;
};

const state = ref<DeleteConfirmationState>({
  confirmLabel: 'Delete',
  message: '',
  onConfirm: null,
  open: false,
  title: 'Confirm deletion',
});

const close = () => {
  state.value = {
    ...state.value,
    open: false,
    onConfirm: null,
  };
};

const confirm = () => {
  const callback = state.value.onConfirm;

  close();

  callback?.();
};

export function useDeleteConfirmation() {
  const confirmDelete = ({
    confirmLabel = 'Delete',
    enabled = true,
    message,
    onConfirm,
    title = 'Confirm deletion',
  }: DeleteConfirmationOptions): boolean => {
    if (!enabled) {
      return false;
    }

    state.value = {
      confirmLabel,
      message,
      onConfirm,
      open: true,
      title,
    };

    return true;
  };

  return {
    closeDeleteConfirmation: close,
    confirmDelete,
    confirmDeleteAction: confirm,
    deleteConfirmation: readonly(state),
  };
}
