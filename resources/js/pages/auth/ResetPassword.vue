<script setup lang="ts">
  import { Form, Head } from "@inertiajs/vue3";
  import { h, ref } from "vue";
  import Button from "@/components/ui/button/Button.vue";
  import Card from "@/components/ui/card/Card.vue";
  import Input from "@/components/ui/input/Input.vue";
  import Spinner from "@/components/ui/spinner/Spinner.vue";
  import AuthLayout from "@/layouts/AuthLayout.vue";
  import { update } from "@/routes/password";
  defineOptions({
    layout: (_: unknown, page: unknown) =>
      h(
        AuthLayout,
        {
          title: "Reset password",
          description: "Please enter your new password below",
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

  <Card variant="default" class="px-6">
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
</template>
