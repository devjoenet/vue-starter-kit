<script setup lang="ts">
  import { Form, Head } from "@inertiajs/vue3";
  import { h } from "vue";
  import PasswordController from "@/actions/App/Http/Controllers/Settings/PasswordController";
  import Heading from "@/components/Heading.vue";
  import Button from "@/components/ui/button/Button.vue";
  import Card from "@/components/ui/card/Card.vue";
  import Input from "@/components/ui/input/Input.vue";
  import AppLayout from "@/layouts/AppLayout.vue";
  import SettingsLayout from "@/layouts/settings/Layout.vue";
  import { edit } from "@/routes/user-password";
  defineOptions({
    layout: (page: unknown) =>
      h(
        AppLayout,
        {
          breadcrumbs: [{ title: "Password settings", href: edit().url }],
        },
        () => h(SettingsLayout, null, () => page),
      ),
  });
</script>

<template>
  <Head title="Password settings" />

  <h1 class="sr-only">Password Settings</h1>

  <Card variant="default" class="px-6">
    <div class="space-y-4">
      <Heading variant="small" title="Update password" description="Ensure your account is using a long, random password to stay secure" />

      <Form
        v-bind="PasswordController.update.form()"
        :options="{
          preserveScroll: true,
        }"
        reset-on-success
        :reset-on-error="['password', 'password_confirmation', 'current_password']"
        class="space-y-4"
        v-slot="{ errors, processing, recentlySuccessful }"
      >
        <Input id="current_password" name="current_password" type="password" label="Current password" variant="outlined" autocomplete="current-password" :state="errors.current_password ? 'error' : 'default'" :message="errors.current_password" />

        <Input id="password" name="password" type="password" label="New password" variant="outlined" autocomplete="new-password" :state="errors.password ? 'error' : 'default'" :message="errors.password" />

        <Input id="password_confirmation" name="password_confirmation" type="password" label="Confirm password" variant="outlined" autocomplete="new-password" :state="errors.password_confirmation ? 'error' : 'default'" :message="errors.password_confirmation" />

        <div class="flex items-center gap-4">
          <Button appearance="filled" :disabled="processing" data-test="update-password-button">Save password</Button>

          <Transition enter-active-class="transition ease-in-out" enter-from-class="opacity-0" leave-active-class="transition ease-in-out" leave-to-class="opacity-0">
            <p v-show="recentlySuccessful" class="text-sm text-success">Saved.</p>
          </Transition>
        </div>
      </Form>
    </div>
  </Card>
</template>
