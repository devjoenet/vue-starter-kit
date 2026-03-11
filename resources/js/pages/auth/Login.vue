<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { h } from 'vue';
import TextLink from '@/components/TextLink.vue';
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
        title: 'Log in to your account',
        description: 'Enter your email and password below to log in',
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
        <p
          v-if="status"
          id="auth-login-status"
          class="text-center text-sm font-medium text-success"
        >
          {{ status }}
        </p>

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
        </Form>
      </div>
    </Card>

    <div
      v-if="canRegister"
      id="auth-login-register-link-row"
      class="text-center text-sm text-muted-foreground"
    >
      Don't have an account?
      <TextLink id="auth-login-register-link" :href="register()" :tabindex="5">
        Sign up
      </TextLink>
    </div>
  </section>
</template>
