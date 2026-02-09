<script setup lang="ts">
  import { Form, Head } from "@inertiajs/vue3";
  import TextLink from "@/components/TextLink.vue";
  import { Button } from "@/components/ui/button";
  import { Card } from "@/components/ui/card";
  import { Input } from "@/components/ui/input";
  import { Spinner } from "@/components/ui/spinner";
  import AuthLayout from "@/layouts/AuthLayout.vue";
  import { login } from "@/routes";
  import { email } from "@/routes/password";

  defineProps<{
    status?: string;
  }>();
</script>

<template>
  <AuthLayout title="Forgot password" description="Enter your email to receive a password reset link">
    <Head title="Forgot password" />

    <Card variant="glass" class="px-6">
      <div class="space-y-4">
        <p v-if="status" class="text-center text-sm font-medium text-success">
          {{ status }}
        </p>

        <Form v-bind="email.form()" v-slot="{ errors, processing }" class="space-y-4">
          <Input id="email" type="email" name="email" label="Email address" variant="outlined" autocomplete="off" autofocus :state="errors.email ? 'error' : 'default'" :message="errors.email" />

          <Button class="w-full" appearance="filled" :disabled="processing" data-test="email-password-reset-link-button">
            <Spinner v-if="processing" />
            Email password reset link
          </Button>
        </Form>
      </div>
    </Card>

    <div class="space-x-1 text-center text-sm text-muted-foreground">
      <span>Or, return to</span>
      <TextLink :href="login()">log in</TextLink>
    </div>
  </AuthLayout>
</template>
