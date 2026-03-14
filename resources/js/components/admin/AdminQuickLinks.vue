<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import Button from '@/components/ui/button/Button.vue';
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
type QuickLinkVariant = 'primary' | 'secondary' | 'info';

const toneClasses: Record<
  QuickLinkTone,
  {
    card: string;
    beam: string;
    badge: string;
    linkVariant: QuickLinkVariant;
  }
> = {
  primary: {
    card: 'border-primary/24 bg-linear-to-br from-primary/18 via-card to-card shadow-(--elevation-1)',
    beam: 'via-primary/70',
    badge:
      'border-primary/24 bg-primary/14 text-primary shadow-[inset_0_1px_0_rgb(255_255_255/0.08)]',
    linkVariant: 'primary',
  },
  secondary: {
    card: 'border-secondary/26 bg-linear-to-br from-secondary/18 via-card to-card shadow-(--elevation-1)',
    beam: 'via-secondary/70',
    badge:
      'border-secondary/24 bg-secondary/16 text-secondary shadow-[inset_0_1px_0_rgb(255_255_255/0.08)]',
    linkVariant: 'secondary',
  },
  accent: {
    card: 'border-accent/24 bg-linear-to-br from-accent/18 via-card to-card shadow-(--elevation-1)',
    beam: 'via-accent/70',
    badge:
      'border-accent/24 bg-accent/16 text-accent shadow-[inset_0_1px_0_rgb(255_255_255/0.08)]',
    linkVariant: 'info',
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
      <p class="text-xs font-semibold tracking-[0.16em] text-primary uppercase">
        Administration
      </p>
      <h2 class="text-2xl font-semibold tracking-tight">
        Manage access with real counts, not placeholder tiles.
      </h2>
      <p class="text-sm leading-6 text-muted-foreground">
        These are the active admin surfaces available in the starter right now.
      </p>
    </div>

    <div v-if="hasLinks" class="grid gap-5 md:grid-cols-3">
      <Card
        v-for="item in links"
        :key="item.title"
        :class="[
          'group relative h-full overflow-hidden px-6 py-5 transition-[transform,box-shadow] duration-200 hover:-translate-y-0.5 hover:shadow-(--elevation-2) motion-reduce:transform-none motion-reduce:transition-none',
          toneClasses[item.tone].card,
        ]"
      >
        <div
          :class="[
            'pointer-events-none absolute inset-x-0 top-0 h-px bg-linear-to-r from-transparent to-transparent',
            toneClasses[item.tone].beam,
          ]"
        />

        <div class="relative flex h-full flex-col justify-between gap-6">
          <div class="flex items-start justify-between gap-6">
            <div>
              <h3 class="text-base font-semibold tracking-tight">
                {{ item.title }}
              </h3>
              <p class="mt-1 text-sm text-muted-foreground">
                {{ item.description }}
              </p>
            </div>

            <div
              :class="[
                'flex min-w-18 items-center justify-center rounded-full border px-3 py-1 text-2xl font-semibold tabular-nums',
                toneClasses[item.tone].badge,
              ]"
            >
              {{ item.count }}
            </div>
          </div>

          <div>
            <Button
              appearance="link"
              :variant="toneClasses[item.tone].linkVariant"
              size="sm"
              as-child
              class="w-fit px-0 transition-colors motion-reduce:transition-none"
            >
              <Link :href="item.href">Open {{ item.title }}</Link>
            </Button>
          </div>
        </div>
      </Card>
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
