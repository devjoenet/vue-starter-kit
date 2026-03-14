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

const toneClasses: Record<QuickLinkTone, string> = {
  primary:
    'border-primary/16 bg-linear-to-br from-primary/10 via-card to-card shadow-(--elevation-1)',
  secondary:
    'border-secondary/20 bg-linear-to-br from-secondary/12 via-card to-card shadow-(--elevation-1)',
  accent:
    'border-accent/20 bg-linear-to-br from-accent/10 via-card to-card shadow-(--elevation-1)',
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
          toneClasses[item.tone],
        ]"
      >
        <div
          class="pointer-events-none absolute inset-x-0 top-0 h-px bg-linear-to-r from-transparent via-primary/40 to-transparent"
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
              class="flex min-w-18 items-center justify-center rounded-full border border-primary/20 bg-background/90 px-3 py-1 text-2xl font-semibold text-primary tabular-nums"
            >
              {{ item.count }}
            </div>
          </div>

          <div>
            <Button
              appearance="link"
              variant="primary"
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
