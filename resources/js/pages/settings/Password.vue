<script setup lang="ts">
import { Form, Head, setLayoutProps } from '@inertiajs/vue3';
import SettingsSectionCard from '@/components/SettingsSectionCard.vue';
import Button from '@/components/ui/button/Button.vue';
import Input from '@/components/ui/input/Input.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { edit, update } from '@/routes/user-password';
defineOptions({
  layout: [AppLayout, SettingsLayout],
});

setLayoutProps({
  breadcrumbs: [{ title: 'Password settings', href: edit().url }],
});
</script>

<template>
  <Head title="Password settings" />

  <h1 class="sr-only">Password Settings</h1>

  <div id="settings-password-page">
    <SettingsSectionCard
      id="settings-password-card"
      title="Update password"
      description="Ensure your account is using a long, random password to stay secure"
    >
      <Form
        id="settings-password-form"
        v-bind="update.form()"
        :options="{
          only: ['flash'],
          preserveScroll: true,
        }"
        reset-on-success
        :reset-on-error="[
          'password',
          'password_confirmation',
          'current_password',
        ]"
        class="space-y-4"
        v-slot="{ errors, processing, recentlySuccessful }"
      >
        <Input
          id="current_password"
          name="current_password"
          type="password"
          label="Current password"
          variant="outlined"
          autocomplete="current-password"
          :state="errors.current_password ? 'error' : 'default'"
          :message="errors.current_password"
        />

        <Input
          id="password"
          name="password"
          type="password"
          label="New password"
          variant="outlined"
          autocomplete="new-password"
          :state="errors.password ? 'error' : 'default'"
          :message="errors.password"
        />

        <Input
          id="password_confirmation"
          name="password_confirmation"
          type="password"
          label="Confirm password"
          variant="outlined"
          autocomplete="new-password"
          :state="errors.password_confirmation ? 'error' : 'default'"
          :message="errors.password_confirmation"
        />

        <div id="settings-password-actions" class="flex items-center gap-4">
          <Button
            id="settings-password-save-button"
            appearance="filled"
            :disabled="processing"
            data-test="update-password-button"
            >Save password</Button
          >

          <Transition
            enter-active-class="transition ease-in-out"
            enter-from-class="opacity-0"
            leave-active-class="transition ease-in-out"
            leave-to-class="opacity-0"
          >
            <p v-show="recentlySuccessful" class="text-sm text-success">
              Saved.
            </p>
          </Transition>
        </div>
      </Form>
    </SettingsSectionCard>
  </div>
</template>
