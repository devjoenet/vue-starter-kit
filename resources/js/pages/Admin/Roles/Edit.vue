<script setup lang="ts">
  import { useForm } from "@inertiajs/vue3";
  import { computed } from "vue";
  import { Button } from "@/components/ui/button";
  import { Card } from "@/components/ui/card";
  import { Input } from "@/components/ui/input";
  import { useAbility } from "@/composables/useAbility";
  import AppLayout from "@/layouts/AppLayout.vue";
  import { dashboard } from "@/routes/admin";
  import { destroy, edit, index, update } from "@/routes/admin/roles";
  import { sync } from "@/routes/admin/roles/permissions";
  import { type BreadcrumbItem } from "@/types";

  const props = defineProps<{
    role: { id: number; name: string };
    permissionsByGroup: Record<string, { id: number; name: string; group: string }[]>;
    rolePermissions: string[];
  }>();

  const { can } = useAbility();
  const canUpdate = computed(() => can("roles.update"));
  const canDelete = computed(() => can("roles.delete"));
  const canAssign = computed(() => can("roles.assignPermissions"));

  const breadcrumbs: BreadcrumbItem[] = [
    {
      title: "Dashboard",
      href: dashboard.url(),
    },
    {
      title: "Roles",
      href: index.url(),
    },
    {
      title: "Edit",
      href: edit.url(props.role.id),
    },
  ];

  const roleForm = useForm({ name: props.role.name });
  const permsForm = useForm({ permissions: [...props.rolePermissions] });

  const updateRole = () => {
    if (!canUpdate.value) return;
    roleForm.put(update.url(props.role.id), { preserveScroll: true });
  };

  const syncPermissions = () => {
    if (!canAssign.value) return;
    permsForm.put(sync.url(props.role.id), { preserveScroll: true });
  };

  const destroyRole = () => {
    if (!canDelete.value) return;
    if (!confirm("Delete this role?")) return;
    roleForm.delete(destroy.url(props.role.id));
  };

  const togglePermission = (name: string) => {
    const idx = permsForm.permissions.indexOf(name);
    if (idx >= 0) permsForm.permissions.splice(idx, 1);
    else permsForm.permissions.push(name);
  };
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-semibold">Edit role</h1>
      <Button variant="text" :disabled="!canDelete" @click="destroyRole">Delete</Button>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-2">
      <Card class="px-6">
        <h2 class="text-lg font-semibold">Details</h2>

        <form class="mt-4 space-y-4" @submit.prevent="updateRole">
          <Input id="edit-role-name" v-model="roleForm.name" name="name" label="Role name" variant="outlined" :disabled="!canUpdate" :state="roleForm.errors.name ? 'error' : 'default'" :message="roleForm.errors.name" />

          <div class="flex justify-end">
            <Button variant="filled" type="submit" :disabled="!canUpdate || roleForm.processing"> Save </Button>
          </div>
        </form>
      </Card>

      <Card class="px-6">
        <div class="flex items-center justify-between">
          <h2 class="text-lg font-semibold">Permissions</h2>
          <Button variant="tonal" :disabled="!canAssign || permsForm.processing" @click="syncPermissions"> Update permissions </Button>
        </div>

        <div class="mt-4 space-y-5">
          <div v-for="(items, group) in permissionsByGroup" :key="group" class="space-y-2">
            <div class="text-sm font-semibold capitalize">{{ group }}</div>

            <label v-for="p in items" :key="p.id" class="flex items-center gap-3 rounded-xl border border-black/5 p-3 dark:border-white/10" :class="!canAssign ? 'opacity-60' : ''">
              <input type="checkbox" class="h-4 w-4" :disabled="!canAssign" :checked="permsForm.permissions.includes(p.name)" @change="togglePermission(p.name)" />
              <span class="text-sm">{{ p.name }}</span>
            </label>
          </div>
        </div>
      </Card>
    </div>
  </AppLayout>
</template>
