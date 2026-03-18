<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { h } from 'vue';
import AuthCard from '@/components/auth/AuthCard.vue';
import AuthFormFootnote from '@/components/auth/AuthFormFootnote.vue';
import TextLink from '@/components/TextLink.vue';
import Alert from '@/components/ui/alert/Alert.vue';
import AlertDescription from '@/components/ui/alert/AlertDescription.vue';
import AlertTitle from '@/components/ui/alert/AlertTitle.vue';
import Button from '@/components/ui/button/Button.vue';
import Spinner from '@/components/ui/spinner/Spinner.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { logout } from '@/routes';
import { send } from '@/routes/verification';
defineOptions({
  layout: (_: unknown, page: unknown) =>
    h(
      AuthLayout,
      {
        title: 'Verify your email address',
        description: 'Confirm your email to keep account access secure and finish setup.',
      },
      () => page,
    ),
});

defineProps<{
  status?: string;
}>();
</script>

<template>
  <Head title="Email verification" />

  <section id="auth-verify-email-page">
    <AuthCard id="auth-verify-email-card">
      <Form id="auth-verify-email-form" v-bind="send.form()" class="space-y-4 text-center" v-slot="{ processing }">
        <Alert v-if="status === 'verification-link-sent'" id="auth-verify-email-status" variant="success">
          <AlertTitle>Verification email sent</AlertTitle>
          <AlertDescription> Use the newest message in your inbox to finish verifying this account. </AlertDescription>
        </Alert>

        <Button id="auth-verify-email-submit-button" appearance="filled" class="min-h-12 w-full" :disabled="processing">
          <Spinner v-if="processing" />
          Send another verification email
        </Button>

        <AuthFormFootnote>Resend only if the previous email did not arrive or has expired.</AuthFormFootnote>

        <TextLink id="auth-verify-email-logout-button" :href="logout()" as="button" class="mx-auto block text-sm"> Sign out </TextLink>
      </Form>
    </AuthCard>
  </section>
</template>
