<script setup lang="ts">
import { Form, Head, setLayoutProps } from '@inertiajs/vue3';
import { ShieldBan, ShieldCheck } from 'lucide-vue-next';
import { onUnmounted, ref } from 'vue';
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

  <h1 class="sr-only">Two-Factor Authentication Settings</h1>

  <div id="settings-two-factor-page">
    <SettingsSectionCard
      id="settings-two-factor-card"
      title="Two-Factor Authentication"
      description="Manage your two-factor authentication settings"
    >
      <div
        v-if="!props.twoFactorEnabled"
        id="settings-two-factor-disabled-state"
        class="flex flex-col items-start gap-4"
      >
        <Badge id="settings-two-factor-disabled-badge" variant="destructive"
          >Disabled</Badge
        >

        <p class="text-muted-foreground">
          When you enable two-factor authentication, you will be prompted for a
          secure pin during login. This pin can be retrieved from a
          TOTP-supported application on your phone.
        </p>

        <div id="settings-two-factor-enable-actions">
          <Button
            v-if="hasSetupData"
            id="settings-two-factor-continue-setup-button"
            appearance="filled"
            @click="showSetupModal = true"
          >
            <ShieldCheck />Continue Setup
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
              <ShieldCheck />Enable 2FA
            </Button>
          </Form>
        </div>
      </div>

      <div
        v-else
        id="settings-two-factor-enabled-state"
        class="flex flex-col items-start gap-4"
      >
        <Badge id="settings-two-factor-enabled-badge" variant="success"
          >Enabled</Badge
        >

        <p class="text-muted-foreground">
          With two-factor authentication enabled, you will be prompted for a
          secure, random pin during login, which you can retrieve from the
          TOTP-supported application on your phone.
        </p>

        <TwoFactorRecoveryCodes id="settings-two-factor-recovery-codes" />

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
      </div>

      <TwoFactorSetupModal
        id="settings-two-factor-setup-modal"
        v-model:isOpen="showSetupModal"
        :requiresConfirmation="props.requiresConfirmation"
        :twoFactorEnabled="props.twoFactorEnabled"
      />
    </SettingsSectionCard>
  </div>
</template>
