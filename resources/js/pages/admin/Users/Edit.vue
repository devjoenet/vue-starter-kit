<script setup lang="ts">
  import { useForm } from "@inertiajs/vue3";
  import { computed } from "vue";
  import { Button } from "@/components/ui/button";
  import { Card } from "@/components/ui/card";
  import { Checkbox } from "@/components/ui/checkbox";
  import { Input } from "@/components/ui/input";
  import { useAbility } from "@/composables/useAbility";
  import AppLayout from "@/layouts/AppLayout.vue";
  import { dashboard } from "@/routes/admin/index";
  import { destroy, edit, index, update } from "@/routes/admin/users";
  import { sync } from "@/routes/admin/users/roles";
  import { type BreadcrumbItem } from "@/types";

  const props = defineProps<{
    user: { id: number; name: string; email: string };
    roles: { id: number; name: string }[];
    userRoles: string[];
  }>();

  const { can } = useAbility();

  const canUpdate = computed(() => can("users.update"));
  const canAssignRoles = computed(() => can("users.assignRoles"));
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
    {
      title: "Edit",
      href: edit.url(props.user.id),
    },
  ];

  const userForm = useForm({
    name: props.user.name,
    email: props.user.email,
    password: "",
  });

  const rolesForm = useForm({
    roles: [...props.userRoles],
  });

  const updateUser = () => {
    if (!canUpdate.value) return;
    userForm.put(update.url(props.user.id), {
      preserveScroll: true,
    });
  };

  const syncRoles = () => {
    if (!canAssignRoles.value) return;
    rolesForm.put(sync.url(props.user.id), {
      preserveScroll: true,
    });
  };

  const destroyUser = () => {
    if (!canDelete.value) return;
    if (!confirm("Delete this user? This is not reversible.")) return;
    userForm.delete(destroy.url(props.user.id));
  };

  const toggleRole = (roleName: string) => {
    const idx = rolesForm.roles.indexOf(roleName);
    if (idx >= 0) rolesForm.roles.splice(idx, 1);
    else rolesForm.roles.push(roleName);
  };
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6">
      <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-2xl font-semibold">Edit user</h1>

        <Button appearance="text" :disabled="!canDelete" @click="destroyUser"> Delete </Button>
      </div>

      <div class="grid gap-6 lg:grid-cols-2">
        <Card variant="glass" class="px-6">
          <h2 class="text-lg font-semibold">Details</h2>

          <form class="mt-4 space-y-4" @submit.prevent="updateUser">
            <Input id="edit-user-name" v-model="userForm.name" name="name" label="Name" variant="outlined" :disabled="!canUpdate" :state="userForm.errors.name ? 'error' : 'default'" :message="userForm.errors.name" />

            <Input id="edit-user-email" v-model="userForm.email" type="email" name="email" label="Email" variant="outlined" :disabled="!canUpdate" :state="userForm.errors.email ? 'error' : 'default'" :message="userForm.errors.email" />

            <Input id="edit-user-password" v-model="userForm.password" type="password" name="password" label="New password (optional)" variant="outlined" :disabled="!canUpdate" :state="userForm.errors.password ? 'error' : 'default'" :message="userForm.errors.password" />

            <div class="flex justify-end">
              <Button appearance="filled" type="submit" :disabled="!canUpdate || userForm.processing"> Save </Button>
            </div>
          </form>
        </Card>

        <Card variant="glass" class="px-6">
          <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold">Roles</h2>
            <Button appearance="tonal" :disabled="!canAssignRoles || rolesForm.processing" @click="syncRoles"> Update roles </Button>
          </div>

          <div class="mt-4 space-y-2 -mx-3">
            <label v-for="r in roles" :key="r.id" class="flex items-center gap-3 rounded-xl border border-black/5 p-3 dark:border-white/10" :class="!canAssignRoles ? 'opacity-60' : ''">
              <Checkbox :disabled="!canAssignRoles" :model-value="rolesForm.roles.includes(r.name)" @update:model-value="() => toggleRole(r.name)" />
              <span class="text-sm">{{ r.name }}</span>
            </label>
          </div>

          <p v-if="rolesForm.errors.roles" class="mt-2 text-xs opacity-80">
            {{ rolesForm.errors.roles }}
          </p>
        </Card>
      </div>
    </div>
  </AppLayout>
</template>
