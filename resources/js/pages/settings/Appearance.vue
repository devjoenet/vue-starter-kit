<script setup lang="ts">
import { Head, setLayoutProps } from '@inertiajs/vue3';
import AppearanceTabs from '@/components/AppearanceTabs.vue';
import Heading from '@/components/Heading.vue';
import Card from '@/components/ui/card/Card.vue';
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

  <Card variant="default" class="px-6">
    <div class="space-y-4">
      <Heading
        variant="small"
        title="Appearance settings"
        description="Update your account's appearance settings"
      />
      <AppearanceTabs />
    </div>
  </Card>
</template>
