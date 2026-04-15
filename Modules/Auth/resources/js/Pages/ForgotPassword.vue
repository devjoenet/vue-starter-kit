<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { h } from 'vue';
import AuthCard from '@/components/auth/AuthCard.vue';
import AuthFormFootnote from '@/components/auth/AuthFormFootnote.vue';
import AuthLinkRow from '@/components/auth/AuthLinkRow.vue';
import Alert from '@/components/ui/alert/Alert.vue';
import AlertDescription from '@/components/ui/alert/AlertDescription.vue';
import AlertTitle from '@/components/ui/alert/AlertTitle.vue';
import Button from '@/components/ui/button/Button.vue';
import Input from '@/components/ui/input/Input.vue';
import Spinner from '@/components/ui/spinner/Spinner.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { login } from '@/routes';
import { email } from '@/routes/password';
defineOptions({
  layout: (_: unknown, page: unknown) =>
    h(
      AuthLayout,
      {
        title: 'Reset your password',
        description: 'Enter the email tied to your account and we will send a secure reset link.',
      },
      () => page,
    ),
});

defineProps<{
  status?: string;
}>();
</script>

<template>
  <Head title="Forgot password" />

  <section id="auth-forgot-password-page" class="space-y-5">
    <AuthCard id="auth-forgot-password-card">
      <div class="space-y-4">
        <Alert v-if="status" id="auth-forgot-password-status" variant="success">
          <AlertTitle>Check your inbox</AlertTitle>
          <AlertDescription>
            {{ status }}
          </AlertDescription>
        </Alert>

        <Form id="auth-forgot-password-form" v-bind="email.form()" v-slot="{ errors, processing }" class="space-y-4">
          <Input id="email" type="email" name="email" label="Email address" variant="outlined" autocomplete="off" autofocus :state="errors.email ? 'error' : 'default'" :message="errors.email" />

          <Button id="auth-forgot-password-submit-button" class="min-h-12 w-full" appearance="filled" :disabled="processing" data-test="email-password-reset-link-button">
            <Spinner v-if="processing" />
            Send reset link
          </Button>

          <AuthFormFootnote>We will send the newest secure reset link to the address above.</AuthFormFootnote>
        </Form>
      </div>
    </AuthCard>

    <AuthLinkRow id="auth-forgot-password-login-link-row" link-id="auth-forgot-password-login-link" :href="login()" prompt="Remembered it?" label="Return to sign in" />
  </section>
</template>
