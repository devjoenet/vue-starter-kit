<script setup lang="ts">
  import { Form, Head } from "@inertiajs/vue3";
  import TextLink from "@/components/TextLink.vue";
  import { Button } from "@/components/ui/button";
  import { Card } from "@/components/ui/card";
  import { Spinner } from "@/components/ui/spinner";
  import AuthLayout from "@/layouts/AuthLayout.vue";
  import { logout } from "@/routes";
  import { send } from "@/routes/verification";

  defineProps<{
    status?: string;
  }>();
</script>

<template>
  <AuthLayout title="Verify email" description="Please verify your email address by clicking on the link we just emailed to you.">
    <Head title="Email verification" />

    <Card variant="glass" class="px-6">
      <Form v-bind="send.form()" class="space-y-4 text-center" v-slot="{ processing }">
        <p v-if="status === 'verification-link-sent'" class="text-sm font-medium text-success">A new verification link has been sent to the email address you provided during registration.</p>

        <Button appearance="filled" class="w-full" :disabled="processing">
          <Spinner v-if="processing" />
          Resend verification email
        </Button>

        <TextLink :href="logout()" as="button" class="mx-auto block text-sm"> Log out </TextLink>
      </Form>
    </Card>
  </AuthLayout>
</template>
