<script setup lang="ts">
import { Form, Head, setLayoutProps } from '@inertiajs/vue3';
import { ShieldBan, ShieldCheck } from 'lucide-vue-next';
import { onUnmounted, ref } from 'vue';
import SettingsActionRow from '@/components/SettingsActionRow.vue';
import SettingsSectionCard from '@/components/SettingsSectionCard.vue';
import TwoFactorRecoveryCodes from '@/components/TwoFactorRecoveryCodes.vue';
import TwoFactorSetupModal from '@/components/TwoFactorSetupModal.vue';
import Badge from '@/components/ui/badge/Badge.vue';
import Button from '@/components/ui/button/Button.vue';
import { useTwoFactorAuth } from '@/composables/useTwoFactorAuth';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { disable, enable, show } from '@/routes/two-factor';
import type { SettingsTwoFactorPageProps } from '@/types/page-props';
defineOptions({
  layout: [AppLayout, SettingsLayout],
});

setLayoutProps({
  breadcrumbs: [{ title: 'Two-Factor Authentication', href: show.url() }],
});

const props = withDefaults(defineProps<SettingsTwoFactorPageProps>(), {
  requiresConfirmation: false,
  twoFactorEnabled: false,
});

const { hasSetupData, clearTwoFactorAuthData } = useTwoFactorAuth();
const showSetupModal = ref<boolean>(false);

onUnmounted(() => {
  clearTwoFactorAuthData();
});
</script>

<template>
  <Head title="Two-Factor Authentication" />

  <div id="settings-two-factor-page">
    <SettingsSectionCard
      id="settings-two-factor-card"
      title="Two-factor authentication"
      description="Protect sign-in with an authenticator app and keep your recovery path current."
      content-class="space-y-5"
    >
      <div
        v-if="!props.twoFactorEnabled"
        id="settings-two-factor-disabled-state"
        class="space-y-4"
      >
        <div class="flex flex-wrap items-center gap-3">
          <Badge id="settings-two-factor-disabled-badge" variant="destructive"
            >Disabled</Badge
          >
          <p class="text-sm text-muted-foreground">
            Add a second factor before this account is used in shared demos or
            client-facing environments.
          </p>
        </div>

        <p class="text-muted-foreground">
          When you enable two-factor authentication, you will connect an
          authenticator app and confirm a one-time code before the setting is
          fully active.
        </p>
      </div>

      <div v-else id="settings-two-factor-enabled-state" class="space-y-5">
        <div class="flex flex-wrap items-center gap-3">
          <Badge id="settings-two-factor-enabled-badge" variant="success"
            >Enabled</Badge
          >
          <p class="text-sm text-muted-foreground">
            This account now requires an authenticator code during sign-in.
          </p>
        </div>

        <p class="text-muted-foreground">
          Keep your recovery codes available so you can regain access if you
          lose your phone or reset your authenticator app.
        </p>

        <TwoFactorRecoveryCodes id="settings-two-factor-recovery-codes" />
      </div>

      <template #footer>
        <SettingsActionRow
          v-if="!props.twoFactorEnabled"
          id="settings-two-factor-enable-actions"
        >
          <template #meta>
            <p class="text-sm text-muted-foreground">
              You will review the QR code and recovery details in the next step.
            </p>
          </template>

          <Button
            v-if="hasSetupData"
            id="settings-two-factor-continue-setup-button"
            appearance="filled"
            @click="showSetupModal = true"
          >
            <ShieldCheck />
            Continue setup
          </Button>

          <Form
            v-else
            id="settings-two-factor-enable-form"
            v-bind="enable.form()"
            :options="{
              only: ['twoFactorEnabled', 'flash'],
              preserveScroll: true,
            }"
            @success="showSetupModal = true"
            #default="{ processing }"
          >
            <Button
              id="settings-two-factor-enable-button"
              type="submit"
              appearance="filled"
              :disabled="processing"
            >
              <ShieldCheck />
              Enable 2FA
            </Button>
          </Form>
        </SettingsActionRow>

        <SettingsActionRow
          v-else
          id="settings-two-factor-disable-actions"
          align="end"
        >
          <Form
            id="settings-two-factor-disable-form"
            v-bind="disable.form()"
            :options="{
              only: ['twoFactorEnabled', 'flash'],
              preserveScroll: true,
            }"
            #default="{ processing }"
          >
            <Button
              id="settings-two-factor-disable-button"
              appearance="filled"
              variant="destructive"
              type="submit"
              :disabled="processing"
            >
              <ShieldBan />
              Disable 2FA
            </Button>
          </Form>
        </SettingsActionRow>
      </template>

      <TwoFactorSetupModal
        id="settings-two-factor-setup-modal"
        v-model:isOpen="showSetupModal"
        :requiresConfirmation="props.requiresConfirmation"
        :twoFactorEnabled="props.twoFactorEnabled"
      />
    </SettingsSectionCard>
  </div>
</template>
