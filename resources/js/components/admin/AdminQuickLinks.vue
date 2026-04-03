<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import Card from '@/components/ui/card/Card.vue';
import { useAbility } from '@/composables/useAbility';
import { index as adminPermissionsIndex } from '@/routes/admin/permissions';
import { index as adminRolesIndex } from '@/routes/admin/roles';
import { index as adminUsersIndex } from '@/routes/admin/users';
import { adminPermissions } from '@/types/admin-permissions';

const props = withDefaults(
  defineProps<{
    counts?: {
      users: number;
      roles: number;
      permissions: number;
    };
  }>(),
  {
    counts: () => ({
      users: 0,
      roles: 0,
      permissions: 0,
    }),
  },
);

const { can } = useAbility();

type QuickLinkTone = 'primary' | 'secondary' | 'accent';

const toneClasses: Record<
  QuickLinkTone,
  {
    row: string;
    beam: string;
    eyebrow: string;
    badge: string;
    action: string;
  }
> = {
  primary: {
    row: 'bg-[linear-gradient(140deg,color-mix(in_oklab,var(--surface-panel-primary)_72%,var(--primary)_28%)_0%,color-mix(in_oklab,var(--surface-panel)_90%,var(--background)_10%)_100%)]',
    beam: 'bg-primary/72 ring-primary/15',
    eyebrow: 'text-primary/82',
    badge: 'bg-primary/14 text-primary',
    action: 'text-primary group-hover:text-primary/80',
  },
  secondary: {
    row: 'bg-[linear-gradient(140deg,color-mix(in_oklab,var(--surface-panel-secondary)_76%,var(--secondary)_24%)_0%,color-mix(in_oklab,var(--surface-panel)_90%,var(--background)_10%)_100%)]',
    beam: 'bg-secondary/72 ring-secondary/15',
    eyebrow: 'text-secondary/86',
    badge: 'bg-secondary/14 text-secondary',
    action: 'text-secondary group-hover:text-secondary/80',
  },
  accent: {
    row: 'bg-[linear-gradient(140deg,color-mix(in_oklab,var(--surface-panel-primary)_64%,var(--accent)_36%)_0%,color-mix(in_oklab,var(--surface-panel)_82%,var(--accent)_18%)_100%)]',
    beam: 'bg-accent ring-accent/15',
    eyebrow: 'text-accent/88',
    badge: 'bg-accent/14 text-accent',
    action: 'text-accent group-hover:text-accent/80',
  },
};

const links = computed(() => {
  const items: Array<{
    id: 'users' | 'roles' | 'permissions';
    title: string;
    description: string;
    href: string;
    count: number;
    tone: QuickLinkTone;
    command: string;
  }> = [];

  if (can(adminPermissions.usersView)) {
    items.push({
      id: 'users',
      title: 'Users',
      description: 'Review who can access the workspace right now.',
      href: adminUsersIndex.url(),
      count: props.counts.users,
      tone: 'primary',
      command: 'Open users',
    });
  }

  if (can(adminPermissions.rolesView)) {
    items.push({
      id: 'roles',
      title: 'Roles',
      description: 'Keep access grouped into reusable policies.',
      href: adminRolesIndex.url(),
      count: props.counts.roles,
      tone: 'accent',
      command: 'Open roles',
    });
  }

  if (can(adminPermissions.permissionsView)) {
    items.push({
      id: 'permissions',
      title: 'Permissions',
      description: 'Define the checks that secure each workflow.',
      href: adminPermissionsIndex.url(),
      count: props.counts.permissions,
      tone: 'secondary',
      command: 'Open permissions',
    });
  }

  return items;
});

const hasLinks = computed(() => links.value.length > 0);
</script>

<template>
  <section class="space-y-4" aria-labelledby="admin-dashboard-quick-links-heading">
    <div class="max-w-sm space-y-2">
      <p class="section-kicker">Command strip</p>
      <h2 id="admin-dashboard-quick-links-heading" class="text-[clamp(1.65rem,2.4vw,2.35rem)] font-semibold tracking-[-0.03em] text-balance">Open the admin surfaces already live in this workspace.</h2>
      <p class="text-sm leading-6 text-muted-foreground">Counts stay attached to the work instead of floating out into separate metric panels.</p>
    </div>

    <nav v-if="hasLinks" id="admin-dashboard-quick-links-nav" aria-labelledby="admin-dashboard-quick-links-heading">
      <ul class="grid gap-3">
        <li v-for="item in links" :key="item.id">
          <Link
            :id="`admin-dashboard-link-${item.id}`"
            :href="item.href"
            :prefetch="['hover', 'click']"
            :class="[
              'group motion-interactive-raise motion-sheen relative flex items-start gap-4 overflow-hidden rounded-[1.4rem] border border-border/70 px-4 py-4 shadow-(--elevation-1) transition-[transform,border-color,background-color] duration-300 ease-(--motion-ease-out-quart) hover:border-border/90 motion-reduce:transform-none motion-reduce:transition-none',
              toneClasses[item.tone].row,
            ]"
          >
            <span :class="['mt-1 size-2.5 shrink-0 rounded-full ring-4', toneClasses[item.tone].beam]" />

            <div class="min-w-0 flex-1">
              <div class="flex items-start justify-between gap-4">
                <div class="min-w-0">
                  <p :class="['text-[0.68rem] font-semibold tracking-[0.18em] uppercase', toneClasses[item.tone].eyebrow]">Active surface</p>
                  <h3 class="mt-2 text-lg font-semibold tracking-tight">
                    {{ item.title }}
                  </h3>
                </div>

                <span :class="['inline-flex min-w-12 justify-center rounded-full px-3 py-1 text-sm font-semibold tabular-nums', toneClasses[item.tone].badge]">
                  {{ item.count }}
                </span>
              </div>

              <p class="mt-2 max-w-sm text-sm leading-6 text-muted-foreground">
                {{ item.description }}
              </p>

              <span :class="['mt-4 inline-flex items-center gap-2 text-sm font-semibold', toneClasses[item.tone].action]">
                {{ item.command }}
                <span aria-hidden="true">→</span>
              </span>
            </div>
          </Link>
        </li>
      </ul>
    </nav>

    <Card v-else id="admin-dashboard-quick-links-empty-state" class="border-dashed px-6 py-6">
      <div class="max-w-2xl space-y-2">
        <h3 class="text-base font-semibold tracking-tight">No admin surfaces are assigned to this account yet.</h3>
        <p class="text-sm leading-6 text-muted-foreground">This workspace can still be reviewed as a product shell, but user, role, and permission tools will appear only when this account is given access to them.</p>
      </div>
    </Card>
  </section>
</template>
