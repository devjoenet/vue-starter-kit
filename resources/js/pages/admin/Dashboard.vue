<script setup lang="ts">
import { Head, setLayoutProps } from '@inertiajs/vue3';
import { adminDashboardBoardSchema } from '@/components/admin/dashboard/dashboard-board.schema';
import DashboardBoard from '@/components/admin/dashboard/DashboardBoard.vue';
import { adminPageLayout } from '@/lib/page-layouts';
import { dashboard } from '@/routes/admin';
import type { AdminDashboardPageProps } from '@/types/admin/dashboard';

defineOptions({
  layout: adminPageLayout,
});

setLayoutProps({
  breadcrumbs: [{ title: 'Dashboard', href: dashboard().url }],
  surfaceVariant: 'dashboard',
});

const props = defineProps<AdminDashboardPageProps>();
</script>

<template>
  <Head title="Dashboard" />

  <section id="admin-dashboard-page" class="motion-stage relative flex h-full flex-1 flex-col gap-5 px-4 pb-4 sm:px-6 sm:pb-6" aria-labelledby="admin-dashboard-heading">
    <header id="admin-dashboard-header" class="sr-only">
      <h1 id="admin-dashboard-heading">Workspace board</h1>
    </header>

    <DashboardBoard :schema="adminDashboardBoardSchema" :sources="props.sources" class="motion-step" style="--motion-order: 1" />
  </section>
</template>
