<script setup lang="ts">
  import { Head } from "@inertiajs/vue3";
  import AppearanceTabs from "@/components/AppearanceTabs.vue";
  import Heading from "@/components/Heading.vue";
  import { Card } from "@/components/ui/card";
  import AppLayout from "@/layouts/AppLayout.vue";
  import SettingsLayout from "@/layouts/settings/Layout.vue";
  import { edit } from "@/routes/appearance";
  import { type BreadcrumbItem } from "@/types";

  const breadcrumbItems: BreadcrumbItem[] = [
    {
      title: "Appearance settings",
      href: edit().url,
    },
  ];
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbItems">
    <Head title="Appearance settings" />

    <h1 class="sr-only">Appearance Settings</h1>

    <SettingsLayout>
      <Card variant="default" class="px-6">
        <div class="space-y-4">
          <Heading variant="small" title="Appearance settings" description="Update your account's appearance settings" />
          <AppearanceTabs />
        </div>
      </Card>
    </SettingsLayout>
  </AppLayout>
</template>
