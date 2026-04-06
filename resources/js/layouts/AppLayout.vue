<script setup lang="ts">
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
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
  <AppSidebarLayout :breadcrumbs="breadcrumbs" :surface-variant="surfaceVariant">
    <slot />
  </AppSidebarLayout>
</template>
