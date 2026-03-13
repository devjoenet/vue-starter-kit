<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { h } from 'vue';
import TextLink from '@/components/TextLink.vue';
import Alert from '@/components/ui/alert/Alert.vue';
import AlertDescription from '@/components/ui/alert/AlertDescription.vue';
import AlertTitle from '@/components/ui/alert/AlertTitle.vue';
import Button from '@/components/ui/button/Button.vue';
import Card from '@/components/ui/card/Card.vue';
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
        description:
          'Enter the email tied to your account and we will send a secure reset link.',
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

  <section id="auth-forgot-password-page" class="space-y-6">
    <Card id="auth-forgot-password-card" variant="default" class="px-6">
      <div class="space-y-4">
        <Alert v-if="status" id="auth-forgot-password-status" variant="success">
          <AlertTitle>Check your inbox</AlertTitle>
          <AlertDescription>
            {{ status }}
          </AlertDescription>
        </Alert>

        <Form
          id="auth-forgot-password-form"
          v-bind="email.form()"
          v-slot="{ errors, processing }"
          class="space-y-4"
        >
          <Input
            id="email"
            type="email"
            name="email"
            label="Email address"
            variant="outlined"
            autocomplete="off"
            autofocus
            :state="errors.email ? 'error' : 'default'"
            :message="errors.email"
          />

          <Button
            id="auth-forgot-password-submit-button"
            class="w-full"
            appearance="filled"
            :disabled="processing"
            data-test="email-password-reset-link-button"
          >
            <Spinner v-if="processing" />
            Send reset link
          </Button>

          <p class="text-center text-xs text-muted-foreground">
            We will send the newest secure reset link to the address above.
          </p>
        </Form>
      </div>
    </Card>

    <div
      id="auth-forgot-password-login-link-row"
      class="space-x-1 text-center text-sm text-muted-foreground"
    >
      <span>Remembered it?</span>
      <TextLink id="auth-forgot-password-login-link" :href="login()">
        Return to sign in
      </TextLink>
    </div>
  </section>
</template>
