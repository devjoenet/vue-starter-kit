type DeleteConfirmationOptions = {
  enabled?: boolean;
  message: string;
  onConfirm: () => void;
};

export function useDeleteConfirmation() {
  const confirmDelete = ({
    enabled = true,
    message,
    onConfirm,
  }: DeleteConfirmationOptions): boolean => {
    if (!enabled || !confirm(message)) {
      return false;
    }

    onConfirm();

    return true;
  };

  return {
    confirmDelete,
  };
}
