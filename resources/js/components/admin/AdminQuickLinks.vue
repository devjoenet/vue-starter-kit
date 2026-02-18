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

  type QuickLinkTone = "sky" | "violet" | "mint";

  const toneClasses: Record<QuickLinkTone, string> = {
    sky: "from-sky-500/15 via-card to-card/80 ring-sky-400/20 hover:shadow-[0_18px_40px_-26px_rgba(56,189,248,0.75)]",
    violet: "from-violet-500/15 via-card to-card/80 ring-violet-400/20 hover:shadow-[0_18px_40px_-26px_rgba(167,139,250,0.75)]",
    mint: "from-emerald-400/15 via-card to-card/80 ring-emerald-300/20 hover:shadow-[0_18px_40px_-26px_rgba(52,211,153,0.7)]",
  };

  const links = computed(() => {
    const items: Array<{ title: string; description: string; href: string; count: number; tone: QuickLinkTone }> = [];

    if (can("users.view")) {
      items.push({
        title: "Users",
        description: "Manage user accounts and access.",
        href: adminUsersIndex.url(),
        count: props.counts.users,
        tone: "sky",
      });
    }

    if (can("roles.view")) {
      items.push({
        title: "Roles",
        description: "Organize permissions into reusable roles.",
        href: adminRolesIndex.url(),
        count: props.counts.roles,
        tone: "violet",
      });
    }

    if (can("permissions.view")) {
      items.push({
        title: "Permissions",
        description: "Define granular access rules.",
        href: adminPermissionsIndex.url(),
        count: props.counts.permissions,
        tone: "mint",
      });
    }

    return items;
  });

  const hasLinks = computed(() => links.value.length > 0);
</script>

<template>
  <section v-if="hasLinks" class="mt-8 space-y-5">
    <Heading title="Administration" description="Manage users, roles, and permissions." />

    <div class="grid gap-5 md:grid-cols-3">
      <Card v-for="item in links" :key="item.title" :class="['group relative h-full overflow-hidden border-border/60 bg-linear-to-br px-6 py-5 shadow-(--elevation-1) ring-1 transition-all duration-200 hover:shadow-(--elevation-3)', toneClasses[item.tone]]">
        <div class="pointer-events-none absolute inset-x-0 top-0 h-px bg-linear-to-r from-primary/0 via-primary/60 to-secondary/0" />
        <div class="pointer-events-none absolute -right-16 top-12 size-40 rounded-full bg-primary/15 blur-3xl transition-opacity duration-300 group-hover:opacity-100 md:opacity-0" />

        <div class="relative flex h-full flex-col justify-between gap-6">
          <div class="flex items-start justify-between gap-6">
            <div>
              <h3 class="text-base font-semibold tracking-tight">{{ item.title }}</h3>
              <p class="mt-1 text-sm text-muted-foreground">{{ item.description }}</p>
            </div>

            <div class="flex min-w-18 items-center justify-center rounded-full border border-primary/30 bg-primary/15 px-3 py-1 text-2xl font-semibold tabular-nums text-primary">
              {{ item.count }}
            </div>
          </div>

          <div>
            <Button appearance="outline" variant="primary" size="sm" as-child class="w-fit shadow-(--elevation-1) transition-all hover:bg-primary/90 hover:shadow-(--elevation-2)">
              <Link :href="item.href">Open</Link>
            </Button>
          </div>
        </div>
      </Card>
    </div>
  </section>
</template>
