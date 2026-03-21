<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';
import AdminQuickLinks from '@/components/admin/AdminQuickLinks.vue';
import { adminPageLayout, setBreadcrumbs } from '@/lib/page-layouts';
import { dashboard } from '@/routes/admin';
import type { AdminDashboardPageProps } from '@/types/page-props';

defineOptions({
  layout: adminPageLayout,
});

setBreadcrumbs({ title: 'Dashboard', href: dashboard().url });

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

  <div id="admin-dashboard-page" class="surface-dashboard-shell motion-stage relative flex h-full flex-1 flex-col gap-6 overflow-hidden rounded-[1.75rem] p-5 sm:p-6">
    <div class="pointer-events-none absolute inset-x-0 top-0 h-72 bg-linear-to-b from-primary/14 via-primary/5 to-transparent" />
    <div class="pointer-events-none absolute top-8 -right-16 size-72 rounded-full bg-accent/12 blur-3xl" />
    <div class="pointer-events-none absolute -bottom-24 left-12 size-72 rounded-full bg-secondary/12 blur-3xl" />

    <header class="surface-dashboard-primary motion-step rounded-[1.75rem] p-6 lg:p-8" style="--motion-order: 0">
      <div class="grid gap-6 xl:grid-cols-[minmax(0,1.15fr)_minmax(16rem,0.85fr)] xl:items-start">
        <div class="space-y-4">
          <p class="section-kicker">Workspace overview</p>
          <h1 class="max-w-4xl text-[clamp(2.4rem,4.8vw,4.4rem)] leading-[0.94] font-semibold tracking-[-0.04em] text-balance">Access, roles, and permissions at a glance.</h1>
          <p class="max-w-2xl text-sm leading-6 text-muted-foreground">This starter stays honest about what it manages today while still reading like a capable command surface for demos, internal tools, and client-facing handoff.</p>
        </div>

        <aside class="surface-dashboard-secondary rounded-[1.4rem] p-5">
          <p class="text-[0.68rem] font-semibold tracking-[0.18em] text-secondary uppercase">Current signal</p>
          <p class="mt-3 text-lg font-semibold tracking-tight text-balance">The dashboard now leads with actual access work instead of starter filler.</p>
          <p class="mt-3 text-sm leading-6 text-muted-foreground">Counts, quick links, and admin status all point at what this workspace can do right now.</p>
        </aside>
      </div>
    </header>

    <AdminQuickLinks id="admin-dashboard-quick-links" :counts="props.counts" class="motion-step" style="--motion-order: 1" />

    <div class="grid gap-4 xl:grid-cols-[minmax(0,1.25fr)_minmax(18rem,0.75fr)]">
      <section id="admin-dashboard-main-panel" class="surface-dashboard-primary motion-step rounded-3xl p-6" style="--motion-order: 2">
        <div class="max-w-2xl space-y-3">
          <p class="section-kicker">Operational overview</p>
          <h2 class="text-2xl font-semibold tracking-tight text-balance">The dashboard now reflects what this workspace actually manages.</h2>
          <p class="text-sm leading-6 text-muted-foreground">Instead of decorative placeholders, this surface now summarizes the current access model and points directly at the parts of the starter that already matter in real admin work.</p>
        </div>

        <dl class="mt-8 grid gap-6 md:grid-cols-3">
          <div v-for="item in accessOverview" :key="item.id" class="rounded-[1.3rem] border border-border/65 bg-background/28 px-4 py-4">
            <dt class="text-xs font-semibold tracking-[0.14em] text-muted-foreground uppercase">
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

      <aside id="admin-dashboard-focus-panel" class="surface-dashboard-secondary motion-step rounded-3xl p-6" style="--motion-order: 3">
        <div class="space-y-3">
          <p class="section-kicker">Current focus</p>
          <h2 class="text-xl font-semibold tracking-tight">What this starter is actively improving</h2>
        </div>

        <ul class="mt-5 space-y-4 text-sm leading-6 text-muted-foreground">
          <li v-for="item in currentFocus" :key="item" class="flex gap-3 border-t border-border/60 pt-4 first:border-t-0 first:pt-0">
            <span class="mt-2 size-2 rounded-full bg-accent ring-4 ring-accent/15" />
            <span>{{ item }}</span>
          </li>
        </ul>
      </aside>
    </div>

    <section id="admin-dashboard-readiness-panel" class="surface-dashboard-secondary motion-step rounded-3xl p-6" style="--motion-order: 4">
      <div class="grid gap-6 lg:grid-cols-[minmax(0,0.8fr)_minmax(0,1.2fr)] lg:items-start">
        <div class="space-y-3">
          <p class="section-kicker">Ready for next use</p>
          <h2 class="text-2xl font-semibold tracking-tight text-balance">A starter should explain what it is ready to become.</h2>
          <p class="text-sm leading-6 text-muted-foreground">This project is positioned to support demos, internal admin tools, and client-facing builds without pretending to be a finished analytics product before the data exists.</p>
        </div>

        <ul class="grid gap-4 md:grid-cols-3">
          <li v-for="item in starterReadiness" :key="item" class="border-t border-border/60 pt-4 text-sm leading-6 text-muted-foreground">
            {{ item }}
          </li>
        </ul>
      </div>
    </section>
  </div>
</template>
