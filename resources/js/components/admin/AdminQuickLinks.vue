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
    card: string;
    beam: string;
  }
> = {
  primary: {
    card: 'bg-[linear-gradient(160deg,var(--surface-panel-primary),var(--surface-panel))]',
    beam: 'bg-primary/70',
  },
  secondary: {
    card: 'bg-[linear-gradient(160deg,var(--surface-panel-secondary),var(--surface-panel))]',
    beam: 'bg-secondary/70',
  },
  accent: {
    card: 'bg-[linear-gradient(160deg,var(--surface-nav),var(--surface-panel))]',
    beam: 'bg-accent/70',
  },
};

const links = computed(() => {
  const items: Array<{
    title: string;
    description: string;
    href: string;
    count: number;
    tone: QuickLinkTone;
  }> = [];

  if (can(adminPermissions.usersView)) {
    items.push({
      title: 'Users',
      description: 'Review who can access the workspace.',
      href: adminUsersIndex.url(),
      count: props.counts.users,
      tone: 'primary',
    });
  }

  if (can(adminPermissions.rolesView)) {
    items.push({
      title: 'Roles',
      description: 'Keep access grouped into reusable policies.',
      href: adminRolesIndex.url(),
      count: props.counts.roles,
      tone: 'secondary',
    });
  }

  if (can(adminPermissions.permissionsView)) {
    items.push({
      title: 'Permissions',
      description: 'Define the checks that secure each workflow.',
      href: adminPermissionsIndex.url(),
      count: props.counts.permissions,
      tone: 'accent',
    });
  }

  return items;
});

const hasLinks = computed(() => links.value.length > 0);
</script>

<template>
  <section class="space-y-4">
    <div class="max-w-xl space-y-2">
      <p class="section-kicker">Administration</p>
      <h2 class="text-2xl font-semibold tracking-tight">
        Manage access with real counts, not placeholder tiles.
      </h2>
      <p class="text-sm leading-6 text-muted-foreground">
        These are the active admin surfaces available in the starter right now.
      </p>
    </div>

    <div
      v-if="hasLinks"
      class="overflow-hidden rounded-[1.75rem] border border-border/70 shadow-(--elevation-1)"
    >
      <div class="grid gap-px bg-border/60 md:grid-cols-3">
        <Link
          v-for="item in links"
          :key="item.title"
          :href="item.href"
          :class="[
            'group motion-interactive-raise motion-sheen relative block px-5 py-5 motion-reduce:transform-none motion-reduce:transition-none',
            toneClasses[item.tone].card,
          ]"
        >
          <span
            :class="[
              'absolute inset-x-0 top-0 h-1 origin-left scale-x-80 transition-transform duration-500 ease-(--motion-ease-out-quint) group-hover:scale-x-100 motion-reduce:transform-none',
              toneClasses[item.tone].beam,
            ]"
          />

          <div class="flex h-full flex-col justify-between gap-6">
            <div class="flex items-start justify-between gap-6">
              <div class="min-w-0">
                <p
                  class="text-xs font-semibold tracking-[0.16em] text-muted-foreground uppercase"
                >
                  Available now
                </p>
                <h3 class="mt-2 text-lg font-semibold tracking-tight">
                  {{ item.title }}
                </h3>
                <p class="mt-2 text-sm leading-6 text-muted-foreground">
                  {{ item.description }}
                </p>
              </div>

              <div
                class="text-3xl font-semibold tracking-tight tabular-nums transition-transform duration-300 ease-(--motion-ease-out-quart) group-hover:-translate-y-0.5 motion-reduce:transform-none"
              >
                {{ item.count }}
              </div>
            </div>

            <div
              class="flex items-center justify-between gap-3 border-t border-border/60 pt-4"
            >
              <span
                class="text-xs font-semibold tracking-[0.16em] text-muted-foreground uppercase"
              >
                Open surface
              </span>
              <span
                class="text-sm font-semibold text-primary transition-[color,transform] duration-300 ease-(--motion-ease-out-quart) group-hover:translate-x-0.5 group-hover:text-primary/80 motion-reduce:transform-none"
              >
                Open {{ item.title }}
              </span>
            </div>
          </div>
        </Link>
      </div>
    </div>

    <Card
      v-else
      id="admin-dashboard-quick-links-empty-state"
      class="border-dashed px-6 py-6"
    >
      <div class="max-w-2xl space-y-2">
        <h3 class="text-base font-semibold tracking-tight">
          No admin surfaces are assigned to this account yet.
        </h3>
        <p class="text-sm leading-6 text-muted-foreground">
          This workspace can still be reviewed as a product shell, but user,
          role, and permission tools will appear only when this account is given
          access to them.
        </p>
      </div>
    </Card>
  </section>
</template>
