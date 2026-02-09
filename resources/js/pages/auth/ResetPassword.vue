<script setup lang="ts">
  import { Form, Head } from "@inertiajs/vue3";
  import { ref } from "vue";
  import { Button } from "@/components/ui/button";
  import { Card } from "@/components/ui/card";
  import { Input } from "@/components/ui/input";
  import { Spinner } from "@/components/ui/spinner";
  import AuthLayout from "@/layouts/AuthLayout.vue";
  import { update } from "@/routes/password";

  const props = defineProps<{
    token: string;
    email: string;
  }>();

  const inputEmail = ref(props.email);
</script>

<template>
  <AuthLayout title="Reset password" description="Please enter your new password below">
    <Head title="Reset password" />

    <Card variant="glass" class="px-6">
      <Form v-bind="update.form()" :transform="(data) => ({ ...data, token, email })" :reset-on-success="['password', 'password_confirmation']" v-slot="{ errors, processing }" class="space-y-4">
        <Input id="email" v-model="inputEmail" type="email" name="email" label="Email" variant="outlined" autocomplete="email" readonly :state="errors.email ? 'error' : 'default'" :message="errors.email" />

        <Input id="password" type="password" name="password" label="Password" variant="outlined" autocomplete="new-password" autofocus :state="errors.password ? 'error' : 'default'" :message="errors.password" />

        <Input id="password_confirmation" type="password" name="password_confirmation" label="Confirm password" variant="outlined" autocomplete="new-password" :state="errors.password_confirmation ? 'error' : 'default'" :message="errors.password_confirmation" />

        <Button type="submit" appearance="filled" class="w-full" :disabled="processing" data-test="reset-password-button">
          <Spinner v-if="processing" />
          Reset password
        </Button>
      </Form>
    </Card>
  </AuthLayout>
</template>
