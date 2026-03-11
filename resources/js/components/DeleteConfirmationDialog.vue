<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import Dialog from '@/components/ui/dialog/Dialog.vue';
import DialogContent from '@/components/ui/dialog/DialogContent.vue';
import DialogDescription from '@/components/ui/dialog/DialogDescription.vue';
import DialogFooter from '@/components/ui/dialog/DialogFooter.vue';
import DialogHeader from '@/components/ui/dialog/DialogHeader.vue';
import DialogTitle from '@/components/ui/dialog/DialogTitle.vue';
import { useDeleteConfirmation } from '@/composables/useDeleteConfirmation';

const { closeDeleteConfirmation, confirmDeleteAction, deleteConfirmation } =
  useDeleteConfirmation();
</script>

<template>
  <Dialog
    :open="deleteConfirmation.open"
    @update:open="
      (open) => {
        if (!open) {
          closeDeleteConfirmation();
        }
      }
    "
  >
    <DialogContent variant="destructive" :show-close-button="false">
      <DialogHeader>
        <DialogTitle>{{ deleteConfirmation.title }}</DialogTitle>
        <DialogDescription>{{ deleteConfirmation.message }}</DialogDescription>
      </DialogHeader>

      <DialogFooter>
        <Button
          appearance="outline"
          variant="muted"
          type="button"
          @click="closeDeleteConfirmation"
        >
          Cancel
        </Button>
        <Button
          appearance="filled"
          variant="destructive"
          type="button"
          @click="confirmDeleteAction"
        >
          {{ deleteConfirmation.confirmLabel }}
        </Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>
