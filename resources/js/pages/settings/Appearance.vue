<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppearanceTabs from '@/components/AppearanceTabs.vue';
import SettingsSectionCard from '@/components/SettingsSectionCard.vue';
import { setBreadcrumbs, settingsPageLayout } from '@/lib/page-layouts';
import { edit } from '@/routes/appearance';
defineOptions({
  layout: settingsPageLayout,
});

setBreadcrumbs({ title: 'Appearance settings', href: edit().url });
</script>

<template>
  <Head title="Appearance settings" />

  <div id="settings-appearance-page">
    <SettingsSectionCard id="settings-appearance-card" title="Appearance" description="Choose the light or dark presentation that best fits how you work.">
      <AppearanceTabs id="settings-appearance-tabs" />
    </SettingsSectionCard>
  </div>
</template>
