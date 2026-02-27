<script setup lang="ts">
  import { Form, Head } from "@inertiajs/vue3";
  import { h } from "vue";
  import Button from "@/components/ui/button/Button.vue";
  import Card from "@/components/ui/card/Card.vue";
  import Input from "@/components/ui/input/Input.vue";
  import Spinner from "@/components/ui/spinner/Spinner.vue";
  import AuthLayout from "@/layouts/AuthLayout.vue";
  import { store } from "@/routes/password/confirm";
  defineOptions({
    layout: (page: unknown) =>
      h(
        AuthLayout,
        {
          title: "Confirm your password",
          description: "This is a secure area of the application. Please confirm your password before continuing.",
        },
        () => page,
      ),
  });
</script>

<template>
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
</template>
