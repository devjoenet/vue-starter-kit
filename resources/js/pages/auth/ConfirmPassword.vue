<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { h } from 'vue';
import AuthCard from '@/components/auth/AuthCard.vue';
import AuthFormFootnote from '@/components/auth/AuthFormFootnote.vue';
import Alert from '@/components/ui/alert/Alert.vue';
import AlertDescription from '@/components/ui/alert/AlertDescription.vue';
import AlertTitle from '@/components/ui/alert/AlertTitle.vue';
import Button from '@/components/ui/button/Button.vue';
import Input from '@/components/ui/input/Input.vue';
import Spinner from '@/components/ui/spinner/Spinner.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { store } from '@/routes/password/confirm';
defineOptions({
  layout: (_: unknown, page: unknown) =>
    h(
      AuthLayout,
      {
        title: 'Confirm access',
        description: 'Re-enter your password before continuing with this sensitive action.',
      },
      () => page,
    ),
});
</script>

<template>
  <Head title="Confirm password" />

  <section id="auth-confirm-password-page">
    <AuthCard id="auth-confirm-password-card">
      <Form id="auth-confirm-password-form" v-bind="store.form()" reset-on-success v-slot="{ errors, processing }" class="space-y-4">
        <Alert variant="info">
          <AlertTitle>Confirm this action</AlertTitle>
          <AlertDescription> This check confirms it is still you before the protected action continues. Enter the current password for this account to proceed. </AlertDescription>
        </Alert>

        <Input id="password" type="password" name="password" label="Password" variant="outlined" required autocomplete="current-password" autofocus :state="errors.password ? 'error' : 'default'" :message="errors.password" />

        <Button id="auth-confirm-password-submit-button" appearance="filled" class="min-h-12 w-full" :disabled="processing" data-test="confirm-password-button">
          <Spinner v-if="processing" />
          Continue securely
        </Button>

        <AuthFormFootnote>After confirmation, you will return directly to the protected step you were trying to finish.</AuthFormFootnote>
      </Form>
    </AuthCard>
  </section>
</template>
