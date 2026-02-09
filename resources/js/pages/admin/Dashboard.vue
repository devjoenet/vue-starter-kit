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
    <div class="relative flex h-full flex-1 flex-col gap-6 overflow-hidden rounded-2xl border border-border/60 bg-linear-to-br from-background via-background to-muted/70 p-6 shadow-(--elevation-2)">
      <div class="pointer-events-none absolute -top-24 right-0 size-72 rounded-full bg-primary/15 blur-3xl" />
      <div class="pointer-events-none absolute -bottom-32 left-0 size-80 rounded-full bg-secondary/15 blur-3xl" />
      <div class="pointer-events-none absolute inset-0 rounded-2xl bg-[radial-gradient(circle_at_top,rgb(28_150_206/0.14),transparent_55%)]" />

      <div class="relative grid auto-rows-[170px] gap-4 md:grid-cols-6">
        <div class="group relative overflow-hidden rounded-xl border border-border/60 bg-card/90 shadow-(--elevation-1) transition-all duration-200 hover:-translate-y-1 hover:shadow-(--elevation-2) md:col-span-4">
          <div class="pointer-events-none absolute inset-0 bg-linear-to-br from-primary/10 via-transparent to-transparent opacity-0 transition-opacity duration-200 group-hover:opacity-100" />
          <PlaceholderPattern />
        </div>
        <div class="group relative overflow-hidden rounded-xl border border-border/60 bg-card/90 shadow-(--elevation-1) transition-all duration-200 hover:-translate-y-1 hover:shadow-(--elevation-2) md:col-span-2">
          <div class="pointer-events-none absolute inset-0 bg-linear-to-br from-secondary/15 via-transparent to-transparent opacity-0 transition-opacity duration-200 group-hover:opacity-100" />
          <PlaceholderPattern />
        </div>
        <div class="group relative overflow-hidden rounded-xl border border-border/60 bg-card/90 shadow-(--elevation-1) transition-all duration-200 hover:-translate-y-1 hover:shadow-(--elevation-2) md:col-span-6">
          <div class="pointer-events-none absolute inset-0 bg-linear-to-br from-accent/15 via-transparent to-transparent opacity-0 transition-opacity duration-200 group-hover:opacity-100" />
          <PlaceholderPattern />
        </div>
      </div>

      <div class="group relative min-h-screen flex-1 overflow-hidden rounded-xl border border-border/60 bg-card/90 shadow-(--elevation-1) transition-all duration-200 hover:-translate-y-1 hover:shadow-(--elevation-2) md:min-h-min">
        <div class="pointer-events-none absolute inset-0 bg-linear-to-br from-primary/10 via-transparent to-transparent opacity-0 transition-opacity duration-200 group-hover:opacity-100" />
        <PlaceholderPattern />
      </div>

      <AdminQuickLinks :counts="counts" />
    </div>
  </AppLayout>
</template>
