<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';
import AdminQuickLinks from '@/components/admin/AdminQuickLinks.vue';
import { adminPageLayout, setBreadcrumbs } from '@/lib/page-layouts';
import { dashboard } from '@/routes/admin';
import type { AdminDashboardPageProps } from '@/types/admin/dashboard';

defineOptions({
  layout: adminPageLayout,
});

setBreadcrumbs({ title: 'Dashboard', href: dashboard().url });

const props = defineProps<AdminDashboardPageProps>();

const formatCountLabel = (count: number, singular: string, plural: string): string => {
  return `${new Intl.NumberFormat().format(count)} ${count === 1 ? singular : plural}`;
};

const accessSummary = computed(() => {
  return `${formatCountLabel(props.counts.users, 'user account', 'user accounts')}, ${formatCountLabel(props.counts.roles, 'role', 'roles')}, and ${formatCountLabel(props.counts.permissions, 'permission', 'permissions')} are already wired into this workspace.`;
});

const operatingPrinciples = [
  'Start in users when you need to confirm who can reach the workspace right now.',
  'Keep roles reusable so demos and real client builds can share the same access vocabulary.',
  'Use permissions for precise workflow gates instead of broad blanket access.',
] as const;

const currentFocus = [
  'Admin surfaces now share calmer controls, cleaner destructive flows, and less decorative filler.',
  'Auth, settings, and workspace pages keep converging on the same shell, motion, and feedback language.',
  'Keyboard order and visible focus are now treated like release criteria instead of a polite suggestion.',
] as const;

const starterReadiness = [
  'Use it as a demo-safe admin surface that shows real access management instead of placeholder analytics.',
  'Use it as the base for internal tools or client portals where roles and permissions are not optional.',
  'Use it as a product shell that can absorb future modules without rewriting the workspace chrome.',
] as const;
</script>

<template>
  <Head title="Dashboard" />

  <div id="admin-dashboard-page" class="surface-dashboard-shell motion-stage relative flex h-full flex-1 flex-col gap-6 overflow-hidden rounded-[1.75rem] p-5 sm:p-6">
    <div class="pointer-events-none absolute inset-x-0 top-0 h-72 bg-linear-to-b from-primary/14 via-primary/5 to-transparent" />
    <div class="pointer-events-none absolute top-8 -right-16 size-72 rounded-full bg-accent/12 blur-3xl" />
    <div class="pointer-events-none absolute -bottom-24 left-12 size-72 rounded-full bg-secondary/12 blur-3xl" />

    <header id="admin-dashboard-hero" class="surface-dashboard-primary motion-step rounded-[1.75rem] p-6 lg:p-8" style="--motion-order: 0" aria-labelledby="admin-dashboard-heading">
      <div class="grid gap-10 xl:grid-cols-[minmax(0,1.05fr)_minmax(20rem,0.95fr)] xl:items-start">
        <section id="admin-dashboard-main-panel" class="max-w-3xl space-y-6">
          <p class="section-kicker">Workspace overview</p>
          <h1 id="admin-dashboard-heading" class="max-w-4xl text-[clamp(2.7rem,5vw,4.7rem)] leading-[0.92] font-semibold tracking-[-0.055em] text-balance">Keep access visible, tidy, and ready for handoff.</h1>
          <p class="max-w-2xl text-sm leading-6 text-muted-foreground sm:text-base sm:leading-7">This dashboard stays focused on the access model the starter already supports, then points straight at the admin surfaces that actually do the work.</p>

          <p class="max-w-2xl border-t border-border/60 pt-5 text-sm leading-6 font-medium text-foreground/88 sm:text-base">
            {{ accessSummary }}
          </p>

          <ul id="admin-dashboard-principles" class="grid gap-3 sm:grid-cols-3">
            <li v-for="item in operatingPrinciples" :key="item" class="rounded-[1.15rem] border border-border/65 bg-background/25 px-4 py-4 text-sm leading-6 text-muted-foreground">
              {{ item }}
            </li>
          </ul>
        </section>

        <aside id="admin-dashboard-command-stage" class="surface-dashboard-secondary rounded-[1.5rem] p-5 sm:p-6" aria-labelledby="admin-dashboard-command-stage-heading">
          <p id="admin-dashboard-command-stage-heading" class="sr-only">Active admin surfaces</p>
          <AdminQuickLinks id="admin-dashboard-quick-links" :counts="props.counts" class="motion-step" style="--motion-order: 1" />
        </aside>
      </div>
    </header>

    <section
      id="admin-dashboard-support-band"
      class="surface-dashboard-secondary motion-step relative overflow-hidden rounded-[1.75rem] px-6 py-6 sm:px-8 sm:py-8 lg:px-10 lg:py-10"
      style="--motion-order: 2"
      aria-labelledby="admin-dashboard-support-heading"
    >
      <div class="grid gap-8 lg:grid-cols-[minmax(0,19rem)_minmax(0,1fr)] lg:gap-10">
        <aside id="admin-dashboard-support-intro" class="max-w-[19rem] border-t border-border/60 pt-4">
          <p class="section-kicker">Keep the shell honest</p>
          <h2 id="admin-dashboard-support-heading" class="mt-4 text-[clamp(1.9rem,2.8vw,2.8rem)] leading-[0.96] font-semibold tracking-[-0.04em] text-balance">
            A useful starter should explain what is live, what is improving, and what it is ready to become.
          </h2>
          <p class="mt-4 text-sm leading-6 text-muted-foreground sm:text-base sm:leading-7">The lower band carries support and forward motion without turning the landing page into generic analytics furniture.</p>
        </aside>

        <div class="grid gap-6 lg:grid-cols-2 lg:gap-8">
          <section id="admin-dashboard-focus-panel" class="border-t border-border/60 pt-4" aria-labelledby="admin-dashboard-focus-heading">
            <p class="section-kicker">Current focus</p>
            <h3 id="admin-dashboard-focus-heading" class="mt-3 text-xl font-semibold tracking-tight text-balance">What is being tightened right now</h3>

            <ul class="mt-5 space-y-4 text-sm leading-6 text-muted-foreground">
              <li v-for="item in currentFocus" :key="item" class="flex gap-3 border-t border-border/55 pt-4 first:border-t-0 first:pt-0">
                <span class="mt-2 size-2 rounded-full bg-accent ring-4 ring-accent/15" />
                <span>{{ item }}</span>
              </li>
            </ul>
          </section>

          <section id="admin-dashboard-readiness-panel" class="border-t border-border/60 pt-4 lg:border-t-0 lg:border-l lg:border-border/60 lg:pt-0 lg:pl-8" aria-labelledby="admin-dashboard-readiness-heading">
            <p class="section-kicker">Ready for next use</p>
            <h3 id="admin-dashboard-readiness-heading" class="mt-3 text-xl font-semibold tracking-tight text-balance">Where this starter already has real traction</h3>

            <ul class="mt-5 space-y-4 text-sm leading-6 text-muted-foreground">
              <li v-for="item in starterReadiness" :key="item" class="border-t border-border/55 pt-4 first:border-t-0 first:pt-0">
                {{ item }}
              </li>
            </ul>
          </section>
        </div>
      </div>
    </section>
  </div>
</template>
