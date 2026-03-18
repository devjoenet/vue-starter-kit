<script setup lang="ts">
import { Form, Head, setLayoutProps, usePage } from '@inertiajs/vue3';
import { CheckCircle2, TriangleAlert } from 'lucide-vue-next';
import { computed } from 'vue';
import DeleteUser from '@/components/DeleteUser.vue';
import SettingsActionRow from '@/components/SettingsActionRow.vue';
import SettingsSectionCard from '@/components/SettingsSectionCard.vue';
import TextLink from '@/components/TextLink.vue';
import Alert from '@/components/ui/alert/Alert.vue';
import AlertDescription from '@/components/ui/alert/AlertDescription.vue';
import AlertTitle from '@/components/ui/alert/AlertTitle.vue';
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

        <Alert
          v-if="props.mustVerifyEmail && !user.email_verified_at"
          variant="warning"
        >
          <TriangleAlert class="size-4" />
          <AlertTitle>Email verification needed</AlertTitle>
          <AlertDescription>
            <p class="text-sm">
              This address has not been verified yet.
              <TextLink
                id="settings-profile-resend-verification-link"
                :href="send()"
                as="button"
              >
                Resend the verification email.
              </TextLink>
            </p>
          </AlertDescription>
        </Alert>

        <Alert
          v-if="props.status === 'verification-link-sent'"
          id="settings-profile-verification-status"
          variant="success"
        >
          <CheckCircle2 class="size-4" />
          <AlertTitle>Verification email sent</AlertTitle>
          <AlertDescription>
            <p class="text-sm">
              A fresh verification link is on its way to this inbox.
            </p>
          </AlertDescription>
        </Alert>

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
