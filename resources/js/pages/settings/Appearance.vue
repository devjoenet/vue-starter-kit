<script setup lang="ts">
import { Head, setLayoutProps } from '@inertiajs/vue3';
import AppearanceTabs from '@/components/AppearanceTabs.vue';
import SettingsSectionCard from '@/components/SettingsSectionCard.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { edit } from '@/routes/appearance';
defineOptions({
  layout: [AppLayout, SettingsLayout],
});

setLayoutProps({
  breadcrumbs: [{ title: 'Appearance settings', href: edit().url }],
});
</script>

<template>
  <Head title="Appearance settings" />

  <h1 class="sr-only">Appearance Settings</h1>

  <div id="settings-appearance-page">
    <SettingsSectionCard
      id="settings-appearance-card"
      title="Appearance"
      description="Choose the light or dark presentation that best fits how you work."
    >
      <AppearanceTabs id="settings-appearance-tabs" />
    </SettingsSectionCard>
  </div>
</template>
