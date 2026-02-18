<script setup lang="ts">
  import { Form, Head } from "@inertiajs/vue3";
  import { Button } from "@/components/ui/button";
  import { Card } from "@/components/ui/card";
  import { Input } from "@/components/ui/input";
  import { Spinner } from "@/components/ui/spinner";
  import AuthLayout from "@/layouts/AuthLayout.vue";
  import { store } from "@/routes/password/confirm";
</script>

<template>
  <AuthLayout title="Confirm your password" description="This is a secure area of the application. Please confirm your password before continuing.">
    <Head title="Confirm password" />

    <Card variant="default" class="px-6">
      <Form v-bind="store.form()" reset-on-success v-slot="{ errors, processing }" class="space-y-4">
        <Input id="password" type="password" name="password" label="Password" variant="outlined" required autocomplete="current-password" autofocus :state="errors.password ? 'error' : 'default'" :message="errors.password" />

        <Button appearance="filled" class="w-full" :disabled="processing" data-test="confirm-password-button">
          <Spinner v-if="processing" />
          Confirm password
        </Button>
      </Form>
    </Card>
  </AuthLayout>
</template>
