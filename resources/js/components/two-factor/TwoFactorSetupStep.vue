<script setup lang="ts">
import { Check, Copy, ScanLine } from 'lucide-vue-next';
import Alert from '@/components/ui/alert/Alert.vue';
import AlertDescription from '@/components/ui/alert/AlertDescription.vue';
import AlertTitle from '@/components/ui/alert/AlertTitle.vue';
import Button from '@/components/ui/button/Button.vue';
import Spinner from '@/components/ui/spinner/Spinner.vue';

defineProps<{
  buttonText: string;
  copied: boolean;
  manualSetupKey: string | null;
  qrCodeSvg: string | null;
  resolvedAppearance: string;
  setupErrorMessages: string[];
}>();

defineEmits<{
  (event: 'continue'): void;
  (event: 'copy-manual-key'): void;
}>();
</script>

<template>
  <div
    class="relative flex w-auto flex-col items-center justify-center space-y-5"
  >
    <Alert v-if="setupErrorMessages.length" variant="destructive">
      <ScanLine class="size-4" />
      <AlertTitle>Unable to prepare two-factor setup</AlertTitle>
      <AlertDescription>
        <p v-if="setupErrorMessages.length === 1" class="text-sm">
          {{ setupErrorMessages[0] }}
        </p>
        <ul v-else class="list-inside list-disc text-sm">
          <li
            v-for="(message, index) in setupErrorMessages"
            :key="`${message}-${index}`"
          >
            {{ message }}
          </li>
        </ul>
      </AlertDescription>
    </Alert>

    <template v-else>
      <div class="relative mx-auto flex max-w-md items-center overflow-hidden">
        <div
          class="relative mx-auto aspect-square w-64 overflow-hidden rounded-lg border border-border"
        >
          <div
            v-if="!qrCodeSvg"
            class="absolute inset-0 z-10 flex aspect-square h-auto w-full animate-pulse items-center justify-center bg-background"
          >
            <Spinner class="size-6" />
          </div>
          <div v-else class="relative z-10 overflow-hidden border p-5">
            <div
              v-html="qrCodeSvg"
              class="flex aspect-square size-full items-center justify-center"
              :style="{
                filter:
                  resolvedAppearance === 'dark'
                    ? 'invert(1) brightness(1.5)'
                    : undefined,
              }"
            />
          </div>
        </div>
      </div>

      <div class="flex w-full items-center space-x-5">
        <Button class="w-full" @click="$emit('continue')">
          {{ buttonText }}
        </Button>
      </div>

      <div class="relative flex w-full items-center justify-center">
        <div class="absolute inset-0 top-1/2 h-px w-full bg-border" />
        <span class="relative bg-card px-2 py-1"
          >or, enter the code manually</span
        >
      </div>

      <div class="flex w-full items-center justify-center space-x-2">
        <div
          class="flex w-full items-stretch overflow-hidden rounded-xl border border-border"
        >
          <div
            v-if="!manualSetupKey"
            class="flex h-full w-full items-center justify-center bg-muted p-3"
          >
            <Spinner />
          </div>
          <template v-else>
            <input
              type="text"
              readonly
              :value="manualSetupKey"
              class="h-full w-full bg-background p-3 text-foreground"
            />
            <button
              type="button"
              :aria-label="
                copied ? 'Manual setup key copied' : 'Copy manual setup key'
              "
              class="relative block h-auto border-l border-border px-3 hover:bg-muted"
              @click="$emit('copy-manual-key')"
            >
              <Check v-if="copied" class="w-4 text-success" />
              <Copy v-else class="w-4" />
            </button>
          </template>
        </div>
      </div>
    </template>
  </div>
</template>
