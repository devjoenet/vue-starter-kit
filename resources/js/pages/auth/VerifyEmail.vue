<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { h } from 'vue';
import TextLink from '@/components/TextLink.vue';
import Button from '@/components/ui/button/Button.vue';
import Card from '@/components/ui/card/Card.vue';
import Spinner from '@/components/ui/spinner/Spinner.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { logout } from '@/routes';
import { send } from '@/routes/verification';
defineOptions({
  layout: (_: unknown, page: unknown) =>
    h(
      AuthLayout,
      {
        title: 'Verify email',
        description:
          'Please verify your email address by clicking on the link we just emailed to you.',
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
    <Card id="auth-verify-email-card" variant="default" class="px-6">
      <Form
        id="auth-verify-email-form"
        v-bind="send.form()"
        class="space-y-4 text-center"
        v-slot="{ processing }"
      >
        <p
          v-if="status === 'verification-link-sent'"
          id="auth-verify-email-status"
          class="text-sm font-medium text-success"
        >
          A new verification link has been sent to the email address you
          provided during registration.
        </p>

        <Button
          id="auth-verify-email-submit-button"
          appearance="filled"
          class="w-full"
          :disabled="processing"
        >
          <Spinner v-if="processing" />
          Resend verification email
        </Button>

        <TextLink
          id="auth-verify-email-logout-button"
          :href="logout()"
          as="button"
          class="mx-auto block text-sm"
        >
          Log out
        </TextLink>
      </Form>
    </Card>
  </section>
</template>
