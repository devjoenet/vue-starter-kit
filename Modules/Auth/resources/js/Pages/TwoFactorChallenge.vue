<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { h, computed, ref } from 'vue';
import AuthCard from '@/components/auth/AuthCard.vue';
import AuthFormFootnote from '@/components/auth/AuthFormFootnote.vue';
import Alert from '@/components/ui/alert/Alert.vue';
import AlertDescription from '@/components/ui/alert/AlertDescription.vue';
import AlertTitle from '@/components/ui/alert/AlertTitle.vue';
import Button from '@/components/ui/button/Button.vue';
import Input from '@/components/ui/input/Input.vue';
import InputOTP from '@/components/ui/input-otp/InputOTP.vue';
import InputOTPGroup from '@/components/ui/input-otp/InputOTPGroup.vue';
import InputOTPSlot from '@/components/ui/input-otp/InputOTPSlot.vue';
import Spinner from '@/components/ui/spinner/Spinner.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { store } from '@/routes/two-factor/login';
import type { TwoFactorConfigContent } from '@/types/auth';
defineOptions({
  layout: (_: unknown, page: unknown) =>
    h(
      AuthLayout,
      {
        title: 'Complete two-factor sign in',
        description: 'Use your authenticator app or a recovery code to verify it is really you.',
      },
      () => page,
    ),
});

const authConfigContent = computed<TwoFactorConfigContent>(() => {
  if (showRecoveryInput.value) {
    return {
      title: 'Recovery Code',
      description: 'Use one of your recovery codes when your authenticator device is unavailable.',
      buttonText: 'Use an authentication code instead',
    };
  }

  return {
    title: 'Authentication Code',
    description: 'Enter the current code from your authenticator application.',
    buttonText: 'Use a recovery code instead',
  };
});

const showRecoveryInput = ref<boolean>(false);

const toggleRecoveryMode = (clearErrors: () => void): void => {
  showRecoveryInput.value = !showRecoveryInput.value;
  clearErrors();
  code.value = '';
};

const code = ref<string>('');
</script>

<template>
  <Head title="Two-Factor Authentication" />

  <section id="auth-two-factor-challenge-page">
    <AuthCard id="auth-two-factor-challenge-card">
      <div class="space-y-4">
        <Alert variant="info">
          <AlertTitle>{{ authConfigContent.title }}</AlertTitle>
          <AlertDescription>
            {{ authConfigContent.description }}
          </AlertDescription>
        </Alert>

        <p id="auth-two-factor-challenge-description" class="text-sm text-muted-foreground">
          {{ showRecoveryInput ? 'Use a one-time recovery code only when your authenticator device is unavailable.' : 'Enter the current six-digit code exactly as it appears in your authenticator app.' }}
        </p>
        <template v-if="!showRecoveryInput">
          <Form id="auth-two-factor-code-form" v-bind="store.form()" class="space-y-4" reset-on-error @error="code = ''" #default="{ errors, processing, clearErrors }">
            <input type="hidden" name="code" :value="code" />

            <div id="auth-two-factor-code-panel" class="flex flex-col items-center justify-center gap-3 text-center">
              <InputOTP id="otp" v-model="code" :maxlength="6" :disabled="processing" autofocus>
                <InputOTPGroup id="auth-two-factor-code-input-group">
                  <InputOTPSlot v-for="index in 6" :key="index" :index="index - 1" />
                </InputOTPGroup>
              </InputOTP>

              <Alert v-if="errors.code" id="auth-two-factor-code-error" variant="destructive" class="w-full text-left">
                <AlertDescription>{{ errors.code }}</AlertDescription>
              </Alert>
            </div>

            <Button id="auth-two-factor-code-submit-button" type="submit" appearance="filled" class="min-h-12 w-full" :disabled="processing">
              <Spinner v-if="processing" />
              Verify and continue
            </Button>

            <AuthFormFootnote>This form accepts only the active code currently shown in your authenticator app.</AuthFormFootnote>

            <div id="auth-two-factor-code-toggle-row" class="text-center text-sm text-muted-foreground">
              <span>or you can </span>
              <button
                id="auth-two-factor-switch-to-recovery-button"
                type="button"
                class="text-foreground underline decoration-border underline-offset-4 transition-colors duration-300 ease-out hover:text-primary hover:decoration-primary/50"
                @click="() => toggleRecoveryMode(clearErrors)"
              >
                {{ authConfigContent.buttonText }}
              </button>
            </div>
          </Form>
        </template>

        <template v-else>
          <Form id="auth-two-factor-recovery-form" v-bind="store.form()" class="space-y-4" reset-on-error #default="{ errors, processing, clearErrors }">
            <Input
              id="auth-two-factor-recovery-code"
              name="recovery_code"
              type="text"
              label="Recovery code"
              variant="outlined"
              :autofocus="showRecoveryInput"
              required
              :state="errors.recovery_code ? 'error' : 'default'"
              :message="errors.recovery_code"
            />

            <Button id="auth-two-factor-recovery-submit-button" type="submit" appearance="filled" class="min-h-12 w-full" :disabled="processing">
              <Spinner v-if="processing" />
              Verify and continue
            </Button>

            <AuthFormFootnote>Recovery codes are one-time use. Reveal a new set in settings if you have already used this one or no longer trust it.</AuthFormFootnote>

            <div id="auth-two-factor-recovery-toggle-row" class="text-center text-sm text-muted-foreground">
              <span>or you can </span>
              <button
                id="auth-two-factor-switch-to-code-button"
                type="button"
                class="text-foreground underline decoration-border underline-offset-4 transition-colors duration-300 ease-out hover:text-primary hover:decoration-primary/50"
                @click="() => toggleRecoveryMode(clearErrors)"
              >
                {{ authConfigContent.buttonText }}
              </button>
            </div>
          </Form>
        </template>
      </div>
    </AuthCard>
  </section>
</template>
