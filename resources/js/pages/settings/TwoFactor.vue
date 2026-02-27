<script setup lang="ts">
  import { Form, Head } from "@inertiajs/vue3";
  import { ShieldBan, ShieldCheck } from "lucide-vue-next";
  import { h, onUnmounted, ref } from "vue";
  import Heading from "@/components/Heading.vue";
  import TwoFactorRecoveryCodes from "@/components/TwoFactorRecoveryCodes.vue";
  import TwoFactorSetupModal from "@/components/TwoFactorSetupModal.vue";
  import Badge from "@/components/ui/badge/Badge.vue";
  import Button from "@/components/ui/button/Button.vue";
  import Card from "@/components/ui/card/Card.vue";
  import { useTwoFactorAuth } from "@/composables/useTwoFactorAuth";
  import AppLayout from "@/layouts/AppLayout.vue";
  import SettingsLayout from "@/layouts/settings/Layout.vue";
  import { disable, enable, show } from "@/routes/two-factor";
  defineOptions({
    layout: (page: unknown) =>
      h(
        AppLayout,
        {
          breadcrumbs: [{ title: "Two-Factor Authentication", href: show.url() }],
        },
        () => h(SettingsLayout, null, () => page),
      ),
  });

  type Props = {
    requiresConfirmation?: boolean;
    twoFactorEnabled?: boolean;
  };

  withDefaults(defineProps<Props>(), {
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

  <Card variant="default" class="px-6">
    <div class="space-y-4">
      <Heading variant="small" title="Two-Factor Authentication" description="Manage your two-factor authentication settings" />

      <div v-if="!twoFactorEnabled" class="flex flex-col items-start gap-4">
        <Badge variant="destructive">Disabled</Badge>

        <p class="text-muted-foreground">When you enable two-factor authentication, you will be prompted for a secure pin during login. This pin can be retrieved from a TOTP-supported application on your phone.</p>

        <div>
          <Button v-if="hasSetupData" appearance="filled" @click="showSetupModal = true"> <ShieldCheck />Continue Setup </Button>

          <Form v-else v-bind="enable.form()" @success="showSetupModal = true" #default="{ processing }">
            <Button type="submit" appearance="filled" :disabled="processing"> <ShieldCheck />Enable 2FA </Button>
          </Form>
        </div>
      </div>

      <div v-else class="flex flex-col items-start gap-4">
        <Badge variant="success">Enabled</Badge>

        <p class="text-muted-foreground">With two-factor authentication enabled, you will be prompted for a secure, random pin during login, which you can retrieve from the TOTP-supported application on your phone.</p>

        <TwoFactorRecoveryCodes />

        <Form v-bind="disable.form()" #default="{ processing }">
          <Button appearance="filled" variant="destructive" type="submit" :disabled="processing">
            <ShieldBan />
            Disable 2FA
          </Button>
        </Form>
      </div>

      <TwoFactorSetupModal v-model:isOpen="showSetupModal" :requiresConfirmation="requiresConfirmation" :twoFactorEnabled="twoFactorEnabled" />
    </div>
  </Card>
</template>
