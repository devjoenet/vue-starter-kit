<script setup lang="ts">
import { Form, Head, Link, setLayoutProps, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import DeleteUser from '@/components/DeleteUser.vue';
import SettingsSectionCard from '@/components/SettingsSectionCard.vue';
import UserIdentityFields from '@/components/UserIdentityFields.vue';
import Button from '@/components/ui/button/Button.vue';
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

  <div v-if="user" class="space-y-6">
    <SettingsSectionCard
      title="Profile information"
      description="Update your name and email address"
    >
      <Form
        v-bind="update.form()"
        :options="{
          only: ['auth', 'flash'],
          preserveScroll: true,
        }"
        class="space-y-4"
        v-slot="{ errors, processing, recentlySuccessful }"
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

        <div v-if="props.mustVerifyEmail && !user.email_verified_at">
          <p class="text-sm text-muted-foreground">
            Your email address is unverified.
            <Link
              :href="send()"
              as="button"
              class="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500"
            >
              Click here to resend the verification email.
            </Link>
          </p>

          <div
            v-if="props.status === 'verification-link-sent'"
            class="mt-2 text-sm font-medium text-success"
          >
            A new verification link has been sent to your email address.
          </div>
        </div>

        <div class="flex items-center gap-4">
          <Button
            appearance="filled"
            :disabled="processing"
            data-test="update-profile-button"
            >Save</Button
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

    <DeleteUser />
  </div>
</template>
