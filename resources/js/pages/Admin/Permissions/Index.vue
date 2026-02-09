<script setup lang="ts">
  import { Link, useForm } from "@inertiajs/vue3";
  import { computed } from "vue";
  import { Button } from "@/components/ui/button";
  import { Card } from "@/components/ui/card";
  import { useAbility } from "@/composables/useAbility";
  import AppLayout from "@/layouts/AppLayout.vue";
  import { dashboard } from "@/routes/admin/index";
  import { create, destroy, edit, index } from "@/routes/admin/permissions";
  import { type BreadcrumbItem } from "@/types";

  const props = defineProps<{
    permissionsByGroup: Record<string, { id: number; name: string; group: string }[]>;
  }>();

  const { can } = useAbility();
  const canCreate = computed(() => can("permissions.create"));
  const canUpdate = computed(() => can("permissions.update"));
  const canDelete = computed(() => can("permissions.delete"));

  const breadcrumbs: BreadcrumbItem[] = [
    {
      title: "Dashboard",
      href: dashboard.url(),
    },
    {
      title: "Permissions",
      href: index.url(),
    },
  ];

  const del = useForm({});

  const destroyPermission = (id: number) => {
    if (!canDelete.value) return;
    if (!confirm("Delete this permission?")) return;
    del.delete(destroy.url(id));
  };
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6">
      <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-2xl font-semibold">Permissions</h1>

        <Button v-if="canCreate" appearance="glass" as-child>
          <Link :href="create.url()">New permission</Link>
        </Button>
      </div>

      <div class="space-y-6">
        <Card v-for="(items, group) in props.permissionsByGroup" :key="group" variant="glass" class="px-6">
          <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold capitalize">{{ group }}</h2>
          </div>

          <div class="mt-4 space-y-2 -mx-3">
            <div v-for="p in items" :key="p.id" class="flex items-center justify-between gap-3 rounded-xl border border-black/5 p-3 dark:border-white/10">
              <div class="text-sm font-medium">{{ p.name }}</div>

              <div class="flex items-center gap-2">
                <Button v-if="canUpdate" appearance="text" size="sm" as-child>
                  <Link :href="edit.url(p.id)">Edit</Link>
                </Button>

                <Button v-if="canDelete" appearance="text" size="sm" @click="destroyPermission(p.id)"> Delete </Button>
              </div>
            </div>
          </div>
        </Card>
      </div>
    </div>
  </AppLayout>
</template>
