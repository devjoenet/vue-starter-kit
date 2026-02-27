<script setup lang="ts">
  import AppLayout from "@/layouts/app/AppSidebarLayout.vue";
  import type { BreadcrumbItem } from "@/types/navigation";
  type Props = {
    breadcrumbs?: BreadcrumbItem[];
  };

  withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
  });
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <slot />
  </AppLayout>
</template>
