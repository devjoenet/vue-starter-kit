<script setup lang="ts">
import AppContent from '@/components/AppContent.vue';
import AppShell from '@/components/AppShell.vue';
import AppSidebar from '@/components/AppSidebar.vue';
import AppSidebarHeader from '@/components/AppSidebarHeader.vue';
import type { BreadcrumbItem } from '@/types/navigation';
import type { AppSurfaceVariant } from '@/types/ui';

type Props = {
  breadcrumbs?: BreadcrumbItem[];
  surfaceVariant?: AppSurfaceVariant;
};

withDefaults(defineProps<Props>(), {
  breadcrumbs: () => [],
  surfaceVariant: 'default',
});
</script>

<template>
  <AppShell variant="sidebar">
    <AppSidebar />
    <AppContent variant="sidebar" :surface-variant="surfaceVariant" class="overflow-x-hidden">
      <AppSidebarHeader :breadcrumbs="breadcrumbs" />
      <slot />
    </AppContent>
  </AppShell>
</template>
