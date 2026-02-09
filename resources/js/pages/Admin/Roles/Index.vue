<script setup lang="ts">
  import { Link } from "@inertiajs/vue3";
  import { computed } from "vue";
  import { Button } from "@/components/ui/button";
  import { Card } from "@/components/ui/card";
  import { useAbility } from "@/composables/useAbility";
  import AppLayout from "@/layouts/AppLayout.vue";
  import { dashboard } from "@/routes/admin/index";
  import { create, edit, index } from "@/routes/admin/roles";
  import { type BreadcrumbItem } from "@/types";

  const props = defineProps<{
    roles: { id: number; name: string }[];
  }>();

  const { can } = useAbility();
  const canCreate = computed(() => can("roles.create"));
  const canUpdate = computed(() => can("roles.update"));

  const breadcrumbs: BreadcrumbItem[] = [
    {
      title: "Dashboard",
      href: dashboard.url(),
    },
    {
      title: "Roles",
      href: index.url(),
    },
  ];
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6">
      <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-2xl font-semibold">Roles</h1>

        <Button v-if="canCreate" appearance="glass" as-child>
          <Link :href="create.url()">New role</Link>
        </Button>
      </div>

      <Card variant="glass" class="px-6">
        <div class="space-y-2 -mx-3">
          <div v-for="r in props.roles" :key="r.id" class="flex items-center justify-between gap-3 rounded-xl border border-black/5 p-3 dark:border-white/10">
            <div class="text-sm font-medium">{{ r.name }}</div>

            <Button v-if="canUpdate" appearance="text" size="sm" as-child>
              <Link :href="edit.url(r.id)">Edit</Link>
            </Button>
          </div>
        </div>
      </Card>
    </div>
  </AppLayout>
</template>
