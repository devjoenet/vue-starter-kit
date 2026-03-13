<script setup lang="ts">
import { Form, Head, setLayoutProps, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import DeleteUser from '@/components/DeleteUser.vue';
import SettingsActionRow from '@/components/SettingsActionRow.vue';
import SettingsSectionCard from '@/components/SettingsSectionCard.vue';
import TextLink from '@/components/TextLink.vue';
import Button from '@/components/ui/button/Button.vue';
import UserIdentityFields from '@/components/UserIdentityFields.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { edit, update } from '@/routes/profile';
import { send } from '@/routes/verification';
import type { SettingsProfilePageProps } from '@/types/page-props';
defineOptions({
  layout: [AppLayout, SettingsLayout],
});

setLayoutProps({
  breadcrumbs: [{ title: 'Profile settings', href: edit().url }],
});

const props = defineProps<SettingsProfilePageProps>();

const page = usePage();
const user = computed(() => page.props.auth.user);
</script>

<template>
  <Head title="Profile settings" />

  <h1 class="sr-only">Profile Settings</h1>

  <div id="settings-profile-page" v-if="user" class="space-y-6">
    <Form
      id="settings-profile-information-form"
      v-bind="update.form()"
      :options="{
        only: ['auth', 'flash'],
        preserveScroll: true,
      }"
      class="space-y-4"
      v-slot="{ errors, processing, recentlySuccessful }"
    >
      <SettingsSectionCard
        id="settings-profile-information-card"
        title="Details"
        description="Update the name and email attached to this account."
        content-class="space-y-5"
      >
        <UserIdentityFields
          name-id="profile-name"
          email-id="profile-email"
          email-label="Email address"
          :name-default-value="user.name"
          :email-default-value="user.email"
          :name-error="errors.name"
          :email-error="errors.email"
          name-required
          email-required
        />

        <div
          v-if="props.mustVerifyEmail && !user.email_verified_at"
          class="rounded-xl border border-warning/20 bg-warning/8 px-4 py-3"
        >
          <p class="text-sm text-muted-foreground">
            Your email address is unverified.
            <TextLink
              id="settings-profile-resend-verification-link"
              :href="send()"
              as="button"
            >
              Click here to resend the verification email.
            </TextLink>
          </p>

          <div
            v-if="props.status === 'verification-link-sent'"
            id="settings-profile-verification-status"
            class="mt-2 text-sm font-medium text-success"
          >
            A new verification link has been sent to your email address.
          </div>
        </div>

        <template #footer>
          <SettingsActionRow
            id="settings-profile-actions"
            :status="recentlySuccessful ? 'Profile updated.' : undefined"
            status-tone="success"
          >
            <Button
              id="settings-profile-save-button"
              appearance="filled"
              :disabled="processing"
              data-test="update-profile-button"
              >Save changes</Button
            >
          </SettingsActionRow>
        </template>
      </SettingsSectionCard>
    </Form>

    <DeleteUser id="settings-profile-delete-account-card" />
  </div>
</template>
