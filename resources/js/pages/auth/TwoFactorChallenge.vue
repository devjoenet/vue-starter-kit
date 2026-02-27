<script setup lang="ts">
  import { Form, Head } from "@inertiajs/vue3";
  import { h, computed, ref } from "vue";
  import Alert from "@/components/ui/alert/Alert.vue";
  import AlertDescription from "@/components/ui/alert/AlertDescription.vue";
  import Button from "@/components/ui/button/Button.vue";
  import Card from "@/components/ui/card/Card.vue";
  import Input from "@/components/ui/input/Input.vue";
  import InputOTP from "@/components/ui/input-otp/InputOTP.vue";
  import InputOTPGroup from "@/components/ui/input-otp/InputOTPGroup.vue";
  import InputOTPSlot from "@/components/ui/input-otp/InputOTPSlot.vue";
  import AuthLayout from "@/layouts/AuthLayout.vue";
  import { store } from "@/routes/two-factor/login";
  import type { TwoFactorConfigContent } from "@/types/auth";
  defineOptions({
    layout: (_: unknown, page: unknown) =>
      h(
        AuthLayout,
        {
          title: "Two-Factor Authentication",
          description: "Complete your two-factor authentication challenge to continue.",
        },
        () => page,
      ),
  });

  const authConfigContent = computed<TwoFactorConfigContent>(() => {
    if (showRecoveryInput.value) {
      return {
        title: "Recovery Code",
        description: "Please confirm access to your account by entering one of your emergency recovery codes.",
        buttonText: "login using an authentication code",
      };
    }

    return {
      title: "Authentication Code",
      description: "Enter the authentication code provided by your authenticator application.",
      buttonText: "login using a recovery code",
    };
  });

  const showRecoveryInput = ref<boolean>(false);

  const toggleRecoveryMode = (clearErrors: () => void): void => {
    showRecoveryInput.value = !showRecoveryInput.value;
    clearErrors();
    code.value = "";
  };

  const code = ref<string>("");
</script>

<template>
  <Head title="Two-Factor Authentication" />

  <Card variant="default" class="px-6">
    <div class="space-y-4">
      <p class="text-sm text-muted-foreground">
        {{ authConfigContent.description }}
      </p>
      <template v-if="!showRecoveryInput">
        <Form v-bind="store.form()" class="space-y-4" reset-on-error @error="code = ''" #default="{ errors, processing, clearErrors }">
          <input type="hidden" name="code" :value="code" />

          <div class="flex flex-col items-center justify-center gap-3 text-center">
            <InputOTP id="otp" v-model="code" :maxlength="6" :disabled="processing" autofocus>
              <InputOTPGroup>
                <InputOTPSlot v-for="index in 6" :key="index" :index="index - 1" />
              </InputOTPGroup>
            </InputOTP>

            <Alert v-if="errors.code" variant="destructive" class="w-full text-left">
              <AlertDescription>{{ errors.code }}</AlertDescription>
            </Alert>
          </div>

          <Button type="submit" appearance="filled" class="w-full" :disabled="processing">Continue</Button>

          <div class="text-center text-sm text-muted-foreground">
            <span>or you can </span>
            <button type="button" class="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500" @click="() => toggleRecoveryMode(clearErrors)">
              {{ authConfigContent.buttonText }}
            </button>
          </div>
        </Form>
      </template>

      <template v-else>
        <Form v-bind="store.form()" class="space-y-4" reset-on-error #default="{ errors, processing, clearErrors }">
          <Input name="recovery_code" type="text" label="Recovery code" variant="outlined" :autofocus="showRecoveryInput" required :state="errors.recovery_code ? 'error' : 'default'" :message="errors.recovery_code" />

          <Button type="submit" appearance="filled" class="w-full" :disabled="processing">Continue</Button>

          <div class="text-center text-sm text-muted-foreground">
            <span>or you can </span>
            <button type="button" class="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500" @click="() => toggleRecoveryMode(clearErrors)">
              {{ authConfigContent.buttonText }}
            </button>
          </div>
        </Form>
      </template>
    </div>
  </Card>
</template>
