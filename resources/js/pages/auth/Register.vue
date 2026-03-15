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
import { store } from '@/routes/register';
defineOptions({
  layout: (_: unknown, page: unknown) =>
    h(
      AuthLayout,
      {
        title: 'Create your access',
        description:
          'Set up an account for demos, internal testing, or secure client-facing workflows.',
      },
      () => page,
    ),
});
</script>

<template>
  <Head title="Register" />

  <section id="auth-register-page" class="space-y-6">
    <Card id="auth-register-card" variant="default" class="px-6">
      <Form
        id="auth-register-form"
        v-bind="store.form()"
        :reset-on-success="['password', 'password_confirmation']"
        v-slot="{ errors, processing }"
        class="space-y-4"
      >
        <Alert variant="info">
          <AlertTitle>Set up account access</AlertTitle>
          <AlertDescription>
            Use this for approved demo setup, internal testing, or client-facing
            workflows that need a secure sign-in.
          </AlertDescription>
        </Alert>

        <Input
          id="name"
          type="text"
          name="name"
          label="Name"
          variant="outlined"
          required
          autofocus
          autocomplete="name"
          :state="errors.name ? 'error' : 'default'"
          :message="errors.name"
        />

        <Input
          id="email"
          type="email"
          name="email"
          label="Email address"
          variant="outlined"
          required
          autocomplete="email"
          :state="errors.email ? 'error' : 'default'"
          :message="errors.email"
        />

        <Input
          id="password"
          type="password"
          name="password"
          label="Password"
          variant="outlined"
          required
          autocomplete="new-password"
          :state="errors.password ? 'error' : 'default'"
          :message="errors.password"
        />

        <Input
          id="password_confirmation"
          type="password"
          name="password_confirmation"
          label="Confirm password"
          variant="outlined"
          required
          autocomplete="new-password"
          :state="errors.password_confirmation ? 'error' : 'default'"
          :message="errors.password_confirmation"
        />

        <Button
          id="auth-register-submit-button"
          type="submit"
          appearance="filled"
          class="w-full"
          :disabled="processing"
          data-test="register-user-button"
        >
          <Spinner v-if="processing" />
          Create account
        </Button>

        <p class="text-center text-xs text-muted-foreground">
          After registration, continue with verification or sign in directly if
          the account is already active.
        </p>
      </Form>
    </Card>

    <div
      id="auth-register-login-link-row"
      class="text-center text-sm text-muted-foreground"
    >
      Already have access?
      <TextLink
        id="auth-register-login-link"
        :href="login()"
        class="underline underline-offset-4"
        >Sign in</TextLink
      >
    </div>
  </section>
</template>
