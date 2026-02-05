<script setup lang="ts">
  import { Link } from "@inertiajs/vue3";
  import { computed } from "vue";
  import Heading from "@/components/Heading.vue";
  import { Button } from "@/components/ui/button";
  import { Card } from "@/components/ui/card";
  import { useAbility } from "@/composables/useAbility";
  import { index as adminPermissionsIndex } from "@/routes/admin/permissions";
  import { index as adminRolesIndex } from "@/routes/admin/roles";
  import { index as adminUsersIndex } from "@/routes/admin/users";

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

  const links = computed(() => {
    const items: Array<{ title: string; description: string; href: string; count: number }> = [];

    if (can("users.view")) {
      items.push({
        title: "Users",
        description: "Manage user accounts and access.",
        href: adminUsersIndex.url(),
        count: props.counts.users,
      });
    }

    if (can("roles.view")) {
      items.push({
        title: "Roles",
        description: "Organize permissions into reusable roles.",
        href: adminRolesIndex.url(),
        count: props.counts.roles,
      });
    }

    if (can("permissions.view")) {
      items.push({
        title: "Permissions",
        description: "Define granular access rules.",
        href: adminPermissionsIndex.url(),
        count: props.counts.permissions,
      });
    }

    return items;
  });

  const hasLinks = computed(() => links.value.length > 0);
</script>

<template>
  <section v-if="hasLinks" class="mt-6 space-y-4">
    <Heading title="Administration" description="Manage users, roles, and permissions." />

    <div class="grid gap-4 md:grid-cols-3">
      <Card v-for="item in links" :key="item.title" class="flex h-full flex-col justify-between gap-4 px-6">
        <div class="flex items-start justify-between gap-6">
          <div>
            <h3 class="text-base font-semibold">{{ item.title }}</h3>
            <p class="mt-1 text-sm text-muted-foreground">{{ item.description }}</p>
          </div>

          <div class="text-3xl font-semibold tabular-nums text-[var(--sc-primary)]">
            {{ item.count }}
          </div>
        </div>

        <div>
          <Button variant="text" as-child>
            <Link :href="item.href">Open</Link>
          </Button>
        </div>
      </Card>
    </div>
  </section>
</template>
