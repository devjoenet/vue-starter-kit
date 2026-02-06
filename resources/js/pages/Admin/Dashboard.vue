<script setup lang="ts">
  import { Head } from "@inertiajs/vue3";
  import AdminQuickLinks from "@/components/admin/AdminQuickLinks.vue";
  import PlaceholderPattern from "@/components/PlaceholderPattern.vue";
  import AppLayout from "@/layouts/AppLayout.vue";
  import { dashboard } from "@/routes";
  import { type BreadcrumbItem } from "@/types";

  defineProps<{
    counts: {
      users: number;
      roles: number;
      permissions: number;
    };
  }>();

  const breadcrumbs: BreadcrumbItem[] = [
    {
      title: "Dashboard",
      href: dashboard().url,
    },
  ];
</script>

<template>
  <Head title="Dashboard" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="relative flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl bg-gradient-to-br from-background via-background to-muted/60 p-4">
      <div class="pointer-events-none absolute inset-0 rounded-xl bg-[radial-gradient(circle_at_top,_rgb(28_150_206_/_0.12),_transparent_55%)]" />
      <div class="relative grid auto-rows-min gap-4 md:grid-cols-3">
        <div class="relative aspect-video overflow-hidden rounded-xl border border-border/70 bg-card shadow-[var(--elevation-1)]">
          <PlaceholderPattern />
        </div>
        <div class="relative aspect-video overflow-hidden rounded-xl border border-border/70 bg-card shadow-[var(--elevation-1)]">
          <PlaceholderPattern />
        </div>
        <div class="relative aspect-video overflow-hidden rounded-xl border border-border/70 bg-card shadow-[var(--elevation-1)]">
          <PlaceholderPattern />
        </div>
      </div>
      <div class="relative min-h-screen flex-1 rounded-xl border border-border/70 bg-card shadow-[var(--elevation-1)] md:min-h-min">
        <PlaceholderPattern />
      </div>

      <AdminQuickLinks :counts="counts" />
    </div>
  </AppLayout>
</template>
