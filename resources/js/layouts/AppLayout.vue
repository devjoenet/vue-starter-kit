<script setup lang="ts">
import { useLayoutProps } from '@inertiajs/vue3';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItem } from '@/types/navigation';

const layout = useLayoutProps<{
  breadcrumbs: BreadcrumbItem[];
}>({
  breadcrumbs: [],
});
</script>

<template>
  <AppSidebarLayout :breadcrumbs="layout.breadcrumbs">
    <slot />
  </AppSidebarLayout>
</template>
