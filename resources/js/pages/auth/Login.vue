<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { h } from 'vue';
import TextLink from '@/components/TextLink.vue';
import Alert from '@/components/ui/alert/Alert.vue';
import AlertDescription from '@/components/ui/alert/AlertDescription.vue';
import AlertTitle from '@/components/ui/alert/AlertTitle.vue';
import Button from '@/components/ui/button/Button.vue';
import Card from '@/components/ui/card/Card.vue';
import Checkbox from '@/components/ui/checkbox/Checkbox.vue';
import Input from '@/components/ui/input/Input.vue';
import Spinner from '@/components/ui/spinner/Spinner.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { register } from '@/routes';
import { store } from '@/routes/login';
import { request } from '@/routes/password';
defineOptions({
  layout: (_: unknown, page: unknown) =>
    h(
      AuthLayout,
      {
        title: 'Access your workspace',
        description:
          'Sign in to continue with your dashboard, client reviews, and internal tools.',
      },
      () => page,
    ),
});

defineProps<{
  status?: string;
  canResetPassword: boolean;
  canRegister: boolean;
}>();
</script>

<template>
  <Head title="Log in" />

  <section id="auth-login-page" class="space-y-6">
    <Card id="auth-login-card" variant="default" class="px-6">
      <div class="space-y-4">
        <Alert v-if="status" id="auth-login-status" variant="success">
          <AlertTitle>Ready to sign in</AlertTitle>
          <AlertDescription>{{ status }}</AlertDescription>
        </Alert>

        <Form
          id="auth-login-form"
          v-bind="store.form()"
          :reset-on-success="['password']"
          v-slot="{ errors, processing }"
          class="space-y-4"
        >
          <Input
            id="email"
            type="email"
            name="email"
            label="Email address"
            variant="outlined"
            required
            autofocus
            :tabindex="1"
            autocomplete="email"
            :state="errors.email ? 'error' : 'default'"
            :message="errors.email"
          />

          <div id="auth-login-password-block" class="space-y-2">
            <div class="flex justify-end">
              <TextLink
                v-if="canResetPassword"
                id="auth-login-forgot-password-link"
                :href="request()"
                class="text-sm"
                :tabindex="5"
              >
                Forgot password?
              </TextLink>
            </div>

            <Input
              id="password"
              type="password"
              name="password"
              label="Password"
              variant="outlined"
              required
              :tabindex="2"
              autocomplete="current-password"
              :state="errors.password ? 'error' : 'default'"
              :message="errors.password"
            />
          </div>

          <label
            id="auth-login-remember-row"
            for="remember"
            class="flex items-center gap-3 text-sm"
          >
            <Checkbox id="remember" name="remember" :tabindex="3" />
            <span>Remember me</span>
          </label>

          <Button
            id="auth-login-submit-button"
            type="submit"
            appearance="filled"
            class="w-full"
            :tabindex="4"
            :disabled="processing"
            data-test="login-button"
          >
            <Spinner v-if="processing" />
            Log in
          </Button>

          <p class="text-center text-xs text-muted-foreground">
            Use this if you already have a workspace or demo account.
          </p>
        </Form>
      </div>
    </Card>

    <div
      v-if="canRegister"
      id="auth-login-register-link-row"
      class="text-center text-sm text-muted-foreground"
    >
      Need access?
      <TextLink id="auth-login-register-link" :href="register()" :tabindex="5">
        Create an account
      </TextLink>
    </div>
  </section>
</template>
