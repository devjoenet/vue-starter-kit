<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import { usePreferredReducedMotion } from '@vueuse/core';
import { Eye, EyeOff, LockKeyhole, RefreshCw } from 'lucide-vue-next';
import { computed, nextTick, onMounted, ref, useTemplateRef } from 'vue';
import Alert from '@/components/ui/alert/Alert.vue';
import AlertDescription from '@/components/ui/alert/AlertDescription.vue';
import AlertTitle from '@/components/ui/alert/AlertTitle.vue';
import Button from '@/components/ui/button/Button.vue';
import { useTwoFactorAuth } from '@/composables/useTwoFactorAuth';
import { normalizeErrorMessages } from '@/lib/errors';
import { regenerateRecoveryCodes } from '@/routes/two-factor';

const { recoveryCodesList, fetchRecoveryCodes, errors } = useTwoFactorAuth();
const isRecoveryCodesVisible = ref<boolean>(false);
const prefersReducedMotion = usePreferredReducedMotion();
const recoveryCodeSectionRef = useTemplateRef('recoveryCodeSectionRef');
const errorMessages = computed(() => normalizeErrorMessages(errors.value));
const isLoadingRecoveryCodes = computed(
  () =>
    isRecoveryCodesVisible.value &&
    !errorMessages.value.length &&
    !recoveryCodesList.value.length,
);
const recoveryStateDescription = computed(() => {
  if (!isRecoveryCodesVisible.value) {
    return 'Reveal them only when you are ready to store them in a secure place.';
  }

  if (isLoadingRecoveryCodes.value) {
    return 'Loading the current recovery codes for this account.';
  }

  return 'Store these before leaving the page. Each code works once.';
});

const toggleRecoveryCodesVisibility = async () => {
  if (!isRecoveryCodesVisible.value && !recoveryCodesList.value.length) {
    await fetchRecoveryCodes();
  }

  isRecoveryCodesVisible.value = !isRecoveryCodesVisible.value;

  if (isRecoveryCodesVisible.value) {
    await nextTick();
    recoveryCodeSectionRef.value?.scrollIntoView({
      behavior: prefersReducedMotion.value ? 'auto' : 'smooth',
      block: 'nearest',
    });
  }
};

onMounted(async () => {
  if (!recoveryCodesList.value.length) {
    await fetchRecoveryCodes();
  }
});
</script>

<template>
  <section
    class="space-y-4 rounded-[calc(var(--radius-lg)-0.125rem)] border border-border/70 bg-muted/35 p-4 sm:p-5"
  >
    <div
      class="flex flex-col gap-3 pb-1 select-none sm:flex-row sm:items-start sm:justify-between"
    >
      <div class="space-y-1">
        <h3
          class="flex items-center gap-3 text-sm font-semibold tracking-tight"
        >
          <LockKeyhole class="size-4" />
          Recovery codes
        </h3>

        <p class="max-w-2xl text-sm text-muted-foreground">
          Recovery codes let you regain access if you lose your 2FA device.
          Store them in a secure password manager.
        </p>
      </div>

      <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
        <Button @click="toggleRecoveryCodesVisibility" class="w-fit">
          <component
            :is="isRecoveryCodesVisible ? EyeOff : Eye"
            class="size-4"
          />
          {{ isRecoveryCodesVisible ? 'Hide' : 'Reveal' }} Recovery Codes
        </Button>

        <Form
          v-if="isRecoveryCodesVisible && recoveryCodesList.length"
          v-bind="regenerateRecoveryCodes.form()"
          method="post"
          :options="{ preserveScroll: true }"
          @success="fetchRecoveryCodes"
          #default="{ processing }"
        >
          <Button variant="secondary" type="submit" :disabled="processing">
            <RefreshCw /> Regenerate Codes
          </Button>
        </Form>
      </div>

      <p class="text-xs text-muted-foreground">
        {{ recoveryStateDescription }}
      </p>
    </div>

    <div
      :class="[
        'grid overflow-hidden transition-[grid-template-rows,opacity] duration-300 ease-out motion-reduce:transition-none',
        isRecoveryCodesVisible
          ? 'grid-rows-[1fr] opacity-100'
          : 'grid-rows-[0fr] opacity-0',
      ]"
    >
      <div class="min-h-0">
        <div v-if="errorMessages.length" class="pt-1">
          <Alert variant="destructive">
            <LockKeyhole class="size-4" />
            <AlertTitle>Unable to load recovery codes</AlertTitle>
            <AlertDescription>
              <p v-if="errorMessages.length === 1" class="text-sm">
                {{ errorMessages[0] }}
              </p>
              <ul v-else class="list-inside list-disc text-sm">
                <li
                  v-for="(message, index) in errorMessages"
                  :key="`${message}-${index}`"
                >
                  {{ message }}
                </li>
              </ul>
            </AlertDescription>
          </Alert>
        </div>
        <div v-else class="space-y-3 pt-1">
          <div
            ref="recoveryCodeSectionRef"
            class="grid gap-2 rounded-xl border border-border/70 bg-background/80 p-4 font-mono text-sm shadow-[var(--elevation-1)]"
          >
            <div v-if="!recoveryCodesList.length" class="space-y-2">
              <div
                v-for="n in 8"
                :key="n"
                class="h-4 animate-pulse rounded bg-muted-foreground/20"
              ></div>
            </div>
            <div v-else v-for="(code, index) in recoveryCodesList" :key="index">
              {{ code }}
            </div>
          </div>
          <p class="text-xs text-muted-foreground select-none">
            Each recovery code can be used once to access your account and will
            be removed after use. Regenerate them any time if you think they may
            no longer be secure.
          </p>
        </div>
      </div>
    </div>
  </section>
</template>
