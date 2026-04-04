<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import DashboardBoard from '@/components/admin/dashboard/DashboardBoard.vue';
import { adminDashboardBoardSchema } from '@/lib/admin-dashboard';
import { adminPageLayout, setBreadcrumbs } from '@/lib/page-layouts';
import { dashboard } from '@/routes/admin';
import type { AdminDashboardPageProps } from '@/types/admin/dashboard';

defineOptions({
  layout: adminPageLayout,
});

setBreadcrumbs({ title: 'Dashboard', href: dashboard().url });

const props = defineProps<AdminDashboardPageProps>();
</script>

<template>
  <Head title="Dashboard" />

  <div id="admin-dashboard-page" class="surface-dashboard-shell motion-stage relative flex h-full flex-1 flex-col gap-5 overflow-hidden rounded-[1.75rem] p-5 sm:p-6">
    <div class="pointer-events-none absolute inset-x-0 top-0 h-56 bg-linear-to-b from-primary/10 via-primary/4 to-transparent" />
    <div class="pointer-events-none absolute top-12 right-0 size-64 rounded-full bg-secondary/10 blur-3xl" />
    <div class="pointer-events-none absolute -bottom-20 left-12 size-64 rounded-full bg-primary/8 blur-3xl" />

    <header id="admin-dashboard-header" class="surface-dashboard-primary motion-step rounded-[1.75rem] px-6 py-6 md:px-8 md:py-8" style="--motion-order: 0" aria-labelledby="admin-dashboard-heading">
      <div class="max-w-3xl space-y-4">
        <p class="section-kicker">Workspace board</p>
        <h1 id="admin-dashboard-heading" class="max-w-4xl text-[clamp(2.5rem,4.8vw,4.6rem)] leading-[0.94] font-semibold tracking-[-0.055em] text-balance">Open the board, then move straight into access work.</h1>
        <p class="max-w-2xl text-sm leading-6 text-muted-foreground sm:text-base sm:leading-7">This launch surface stays lean on purpose: lead with users, roles, and permissions now, then add richer widgets only when the data deserves the space.</p>
      </div>
    </header>

    <DashboardBoard :schema="adminDashboardBoardSchema" :sources="props.sources" class="motion-step" style="--motion-order: 1" />
  </div>
</template>
