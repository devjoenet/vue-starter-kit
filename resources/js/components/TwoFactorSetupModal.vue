<script setup lang="ts">
import { useClipboard } from '@vueuse/core';
import { ScanLine } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import TwoFactorConfirmationForm from '@/components/two-factor/TwoFactorConfirmationForm.vue';
import TwoFactorSetupStep from '@/components/two-factor/TwoFactorSetupStep.vue';
import Dialog from '@/components/ui/dialog/Dialog.vue';
import DialogContent from '@/components/ui/dialog/DialogContent.vue';
import DialogDescription from '@/components/ui/dialog/DialogDescription.vue';
import DialogHeader from '@/components/ui/dialog/DialogHeader.vue';
import DialogTitle from '@/components/ui/dialog/DialogTitle.vue';
import { useAppearance } from '@/composables/useAppearance';
import { useTwoFactorAuth } from '@/composables/useTwoFactorAuth';
import { normalizeErrorMessages } from '@/lib/errors';
import type { TwoFactorConfigContent } from '@/types/auth';
type Props = {
  requiresConfirmation: boolean;
  twoFactorEnabled: boolean;
};

const { resolvedAppearance } = useAppearance();

const props = defineProps<Props>();
const isOpen = defineModel<boolean>('isOpen');

const { copy, copied } = useClipboard();
const { qrCodeSvg, manualSetupKey, clearSetupData, fetchSetupData, errors } =
  useTwoFactorAuth();
const setupErrorMessages = computed(() => normalizeErrorMessages(errors.value));

const showVerificationStep = ref(false);

const modalConfig = computed<TwoFactorConfigContent>(() => {
  if (props.twoFactorEnabled) {
    return {
      title: 'Two-Factor Authentication Enabled',
      description:
        'Two-factor authentication is now enabled. Scan the QR code or enter the setup key in your authenticator app.',
      buttonText: 'Close',
    };
  }

  if (showVerificationStep.value) {
    return {
      title: 'Verify Authentication Code',
      description: 'Enter the 6-digit code from your authenticator app',
      buttonText: 'Continue',
    };
  }

  return {
    title: 'Enable Two-Factor Authentication',
    description:
      'To finish enabling two-factor authentication, scan the QR code or enter the setup key in your authenticator app',
    buttonText: 'Continue',
  };
});

const handleModalNextStep = () => {
  if (props.requiresConfirmation) {
    showVerificationStep.value = true;
    return;
  }

  clearSetupData();
  isOpen.value = false;
};

const resetModalState = () => {
  if (props.twoFactorEnabled) {
    clearSetupData();
  }

  showVerificationStep.value = false;
};

watch(
  () => isOpen.value,
  async (isOpen) => {
    if (!isOpen) {
      resetModalState();
      return;
    }

    if (!qrCodeSvg.value) {
      await fetchSetupData();
    }
  },
);
</script>

<template>
  <Dialog :open="isOpen" @update:open="isOpen = $event">
    <DialogContent class="sm:max-w-md">
      <DialogHeader class="flex items-center justify-center">
        <div
          class="mb-3 w-auto rounded-full border border-border bg-card p-0.5 shadow-sm"
        >
          <div
            class="relative overflow-hidden rounded-full border border-border bg-muted p-2.5"
          >
            <div class="absolute inset-0 grid grid-cols-5 opacity-50">
              <div
                v-for="i in 5"
                :key="`col-${i}`"
                class="border-r border-border last:border-r-0"
              />
            </div>
            <div class="absolute inset-0 grid grid-rows-5 opacity-50">
              <div
                v-for="i in 5"
                :key="`row-${i}`"
                class="border-b border-border last:border-b-0"
              />
            </div>
            <ScanLine class="relative z-20 size-6 text-foreground" />
          </div>
        </div>
        <DialogTitle>{{ modalConfig.title }}</DialogTitle>
        <DialogDescription class="text-center">
          {{ modalConfig.description }}
        </DialogDescription>
      </DialogHeader>

      <TwoFactorSetupStep
        v-if="!showVerificationStep"
        :button-text="modalConfig.buttonText"
        :copied="copied"
        :manual-setup-key="manualSetupKey"
        :qr-code-svg="qrCodeSvg"
        :resolved-appearance="resolvedAppearance"
        :setup-error-messages="setupErrorMessages"
        @continue="handleModalNextStep"
        @copy-manual-key="copy(manualSetupKey || '')"
      />

      <TwoFactorConfirmationForm
        v-else
        @back="showVerificationStep = false"
        @success="isOpen = false"
      />
    </DialogContent>
  </Dialog>
</template>
