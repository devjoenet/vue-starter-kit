<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { h, ref } from 'vue';
import Alert from '@/components/ui/alert/Alert.vue';
import AlertDescription from '@/components/ui/alert/AlertDescription.vue';
import AlertTitle from '@/components/ui/alert/AlertTitle.vue';
import Button from '@/components/ui/button/Button.vue';
import Card from '@/components/ui/card/Card.vue';
import Input from '@/components/ui/input/Input.vue';
import Spinner from '@/components/ui/spinner/Spinner.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { update } from '@/routes/password';
defineOptions({
  layout: (_: unknown, page: unknown) =>
    h(
      AuthLayout,
      {
        title: 'Choose a new password',
        description:
          'Set a new password for this account and return to work with minimal interruption.',
      },
      () => page,
    ),
});

const props = defineProps<{
  token: string;
  email: string;
}>();

const inputEmail = ref(props.email);
</script>

<template>
  <Head title="Reset password" />

  <section id="auth-reset-password-page">
    <Card
      id="auth-reset-password-card"
      variant="default"
      class="surface-auth-card px-6 py-6"
    >
      <Form
        id="auth-reset-password-form"
        v-bind="update.form()"
        :transform="(data) => ({ ...data, token, email })"
        :reset-on-success="['password', 'password_confirmation']"
        v-slot="{ errors, processing }"
        class="space-y-4"
      >
        <Alert variant="info">
          <AlertTitle>Choose the password you want to use next</AlertTitle>
          <AlertDescription>
            You are resetting the password for {{ email }}. Save the new
            password only when you are ready to use it immediately.
          </AlertDescription>
        </Alert>

        <Input
          id="email"
          v-model="inputEmail"
          type="email"
          name="email"
          label="Email"
          variant="outlined"
          autocomplete="email"
          readonly
          :state="errors.email ? 'error' : 'default'"
          :message="errors.email"
        />

        <Input
          id="password"
          type="password"
          name="password"
          label="Password"
          variant="outlined"
          autocomplete="new-password"
          autofocus
          :state="errors.password ? 'error' : 'default'"
          :message="errors.password"
        />

        <Input
          id="password_confirmation"
          type="password"
          name="password_confirmation"
          label="Confirm password"
          variant="outlined"
          autocomplete="new-password"
          :state="errors.password_confirmation ? 'error' : 'default'"
          :message="errors.password_confirmation"
        />

        <Button
          id="auth-reset-password-submit-button"
          type="submit"
          appearance="filled"
          class="min-h-12 w-full"
          :disabled="processing"
          data-test="reset-password-button"
        >
          <Spinner v-if="processing" />
          Save new password
        </Button>

        <p class="text-center text-xs text-muted-foreground">
          Once saved, return to sign in with the updated password. The reset
          link will no longer be needed after that.
        </p>
      </Form>
    </Card>
  </section>
</template>
