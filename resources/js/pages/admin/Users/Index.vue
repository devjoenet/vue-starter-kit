<script setup lang="ts">
  import { Link, usePage } from "@inertiajs/vue3";
  import { computed } from "vue";
  import { Button } from "@/components/ui/button";
  import { Card } from "@/components/ui/card";
  import { useAbility } from "@/composables/useAbility";
  import AppLayout from "@/layouts/AppLayout.vue";
  import { dashboard } from "@/routes/admin/index";
  import { create, destroy, edit, index } from "@/routes/admin/users";
  import { type BreadcrumbItem } from "@/types";

  const props = defineProps<{
    users: any;
  }>();

  const { can } = useAbility();
  const page = usePage();

  const canCreate = computed(() => can("users.create"));
  const canUpdate = computed(() => can("users.update"));
  const canDelete = computed(() => can("users.delete"));

  const breadcrumbs: BreadcrumbItem[] = [
    {
      title: "Dashboard",
      href: dashboard.url(),
    },
    {
      title: "Users",
      href: index.url(),
    },
  ];
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 px-4">
      <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-2xl font-semibold">Users</h1>

        <Button v-if="canCreate" appearance="glass" as-child>
          <Link :href="create.url()">New user</Link>
        </Button>
      </div>

      <Card variant="glass" class="overflow-hidden py-0">
        <table class="w-full text-sm">
          <thead class="text-left text-xs font-semibold uppercase tracking-wide opacity-60">
            <tr>
              <th class="px-6 py-4">Name</th>
              <th class="px-6 py-4">Email</th>
              <th class="px-6 py-4 text-right">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="u in users.data" :key="u.id" class="border-t border-black/5 dark:border-white/10">
              <td class="px-6 py-4 font-medium">{{ u.name }}</td>
              <td class="px-6 py-4 text-sm opacity-80">{{ u.email }}</td>
              <td class="px-6 py-4">
                <div class="flex items-center justify-end gap-2">
                  <Button v-if="canUpdate" appearance="text" size="sm" as-child>
                    <Link :href="edit.url(u.id)">Edit</Link>
                  </Button>

                  <form v-if="canDelete" class="inline-flex" method="post" :action="destroy.url(u.id)">
                    <input type="hidden" name="_method" value="delete" />
                    <input type="hidden" name="_token" :value="page.props.csrf_token" />
                    <Button type="submit" appearance="text" size="sm">Delete</Button>
                  </form>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </Card>
    </div>
  </AppLayout>
</template>
