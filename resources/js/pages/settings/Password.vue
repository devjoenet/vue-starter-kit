<script setup lang="ts">
import { Form, Head, setLayoutProps } from '@inertiajs/vue3';
import SettingsActionRow from '@/components/SettingsActionRow.vue';
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

  <div id="settings-password-page">
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
      <SettingsSectionCard
        id="settings-password-card"
        title="Password"
        description="Update the credential used to sign in to this account."
        content-class="space-y-4"
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

        <template #footer>
          <SettingsActionRow
            id="settings-password-actions"
            :status="recentlySuccessful ? 'Password updated.' : undefined"
            status-tone="success"
          >
            <template #meta>
              <p class="text-sm text-muted-foreground">
                Password fields clear after a successful save.
              </p>
            </template>

            <Button
              id="settings-password-save-button"
              appearance="filled"
              :disabled="processing"
              data-test="update-password-button"
              >Save password</Button
            >
          </SettingsActionRow>
        </template>
      </SettingsSectionCard>
    </Form>
  </div>
</template>
