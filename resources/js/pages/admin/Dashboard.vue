<script setup lang="ts">
import { Head, setLayoutProps } from '@inertiajs/vue3';
import { computed } from 'vue';
import AdminQuickLinks from '@/components/admin/AdminQuickLinks.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes/admin';
import type { AdminDashboardPageProps } from '@/types/page-props';

defineOptions({
  layout: AppLayout,
});

setLayoutProps({
  breadcrumbs: [{ title: 'Dashboard', href: dashboard().url }],
});

const props = defineProps<AdminDashboardPageProps>();

const accessOverview = computed(() => [
  {
    id: 'users',
    value: props.counts.users,
    label: 'User accounts',
    description: 'People who can access the workspace today.',
  },
  {
    id: 'roles',
    value: props.counts.roles,
    label: 'Role definitions',
    description: 'Reusable access groups that keep permissions manageable.',
  },
  {
    id: 'permissions',
    value: props.counts.permissions,
    label: 'Permission rules',
    description: 'Granular checks available to shape secure workflows.',
  },
]);

const currentFocus = [
  'Identity and access management now have shared table controls, cleaner destructive flows, and stronger feedback patterns.',
  'Auth, settings, and shell work are being aligned so the starter feels client-ready instead of scaffolded.',
  'The UI system is being hardened around reusable tokens, accessible controls, and more intentional empty states.',
];

const starterReadiness = [
  'Use it as a marketing-facing demo that shows polished Laravel, Inertia, and Vue implementation quality.',
  'Use it as a foundation for admin panels, internal tools, and client portals where access control matters.',
  'Use it as a proving ground for interface patterns before they are adapted into real client builds.',
];
</script>

<template>
  <Head title="Dashboard" />

  <div
    id="admin-dashboard-page"
    class="relative flex h-full flex-1 flex-col gap-6 overflow-hidden rounded-2xl border border-border/60 bg-linear-to-br from-background via-background to-primary/12 p-6 shadow-(--elevation-2)"
  >
    <div
      class="pointer-events-none absolute inset-x-0 top-0 h-72 bg-linear-to-b from-primary/14 via-primary/5 to-transparent"
    />
    <div
      class="pointer-events-none absolute top-8 -right-16 size-72 rounded-full bg-accent/12 blur-3xl"
    />
    <div
      class="pointer-events-none absolute -bottom-24 left-12 size-72 rounded-full bg-secondary/12 blur-3xl"
    />

    <AdminQuickLinks id="admin-dashboard-quick-links" :counts="props.counts" />

    <div
      class="grid gap-4 xl:grid-cols-[minmax(0,1.25fr)_minmax(18rem,0.75fr)]"
    >
      <section
        id="admin-dashboard-main-panel"
        class="rounded-2xl border border-border/70 bg-linear-to-br from-card/96 via-card/92 to-primary/10 p-6 shadow-(--elevation-1)"
      >
        <div class="max-w-2xl space-y-3">
          <p
            class="text-xs font-semibold tracking-[0.16em] text-primary uppercase"
          >
            Operational overview
          </p>
          <h2 class="text-2xl font-semibold tracking-tight text-balance">
            The dashboard now reflects what this workspace actually manages.
          </h2>
          <p class="text-sm leading-6 text-muted-foreground">
            Instead of decorative placeholders, this surface now summarizes the
            current access model and points directly at the parts of the starter
            that already matter in real admin work.
          </p>
        </div>

        <dl class="mt-6 grid gap-4 md:grid-cols-3">
          <div
            v-for="item in accessOverview"
            :key="item.id"
            class="rounded-2xl border border-border/60 bg-linear-to-br from-background/96 to-primary/8 p-4"
          >
            <dt
              class="text-xs font-semibold tracking-[0.14em] text-primary uppercase"
            >
              {{ item.label }}
            </dt>
            <dd class="mt-3 text-3xl font-semibold tracking-tight tabular-nums">
              {{ item.value }}
            </dd>
            <p class="mt-2 text-sm leading-6 text-muted-foreground">
              {{ item.description }}
            </p>
          </div>
        </dl>
      </section>

      <aside
        id="admin-dashboard-focus-panel"
        class="rounded-2xl border border-border/70 bg-linear-to-br from-background/96 to-accent/10 p-6 shadow-(--elevation-1)"
      >
        <div class="space-y-3">
          <p
            class="text-xs font-semibold tracking-[0.16em] text-primary uppercase"
          >
            Current focus
          </p>
          <h2 class="text-xl font-semibold tracking-tight">
            What this starter is actively improving
          </h2>
        </div>

        <ul class="mt-5 space-y-4 text-sm leading-6 text-muted-foreground">
          <li
            v-for="item in currentFocus"
            :key="item"
            class="rounded-2xl border border-border/60 bg-linear-to-br from-card/92 to-accent/8 px-4 py-3"
          >
            {{ item }}
          </li>
        </ul>
      </aside>
    </div>

    <section
      id="admin-dashboard-readiness-panel"
      class="rounded-2xl border border-border/70 bg-linear-to-br from-card/96 via-card/92 to-secondary/8 p-6 shadow-(--elevation-1)"
    >
      <div
        class="grid gap-6 lg:grid-cols-[minmax(0,0.8fr)_minmax(0,1.2fr)] lg:items-start"
      >
        <div class="space-y-3">
          <p
            class="text-xs font-semibold tracking-[0.16em] text-primary uppercase"
          >
            Ready for next use
          </p>
          <h2 class="text-2xl font-semibold tracking-tight text-balance">
            A starter should explain what it is ready to become.
          </h2>
          <p class="text-sm leading-6 text-muted-foreground">
            This project is positioned to support demos, internal admin tools,
            and client-facing builds without pretending to be a finished
            analytics product before the data exists.
          </p>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
          <article
            v-for="item in starterReadiness"
            :key="item"
            class="rounded-2xl border border-border/60 bg-linear-to-br from-background/96 to-secondary/8 p-4 text-sm leading-6 text-muted-foreground"
          >
            {{ item }}
          </article>
        </div>
      </div>
    </section>
  </div>
</template>
