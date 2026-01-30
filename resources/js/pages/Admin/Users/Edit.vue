<script setup lang="ts">
  import { useForm } from "@inertiajs/vue3";
  import { computed } from "vue";
  import { useAbility } from "@/composables/useAbility";
  import AdminLayout from "@/layouts/AdminLayout.vue";
  import { update, destroy } from "@/routes/admin/users";
  import { sync } from "@/routes/admin/users/roles";
  import Button from "@/components/md3/Button.vue";
  import Card from "@/components/md3/Card.vue";
  import TextField from "@/components/md3/TextField.vue";

  const props = defineProps<{
    user: { id: number; name: string; email: string };
    roles: { id: number; name: string }[];
    userRoles: string[];
  }>();

  const { can } = useAbility();

  const canUpdate = computed(() => can("users.update"));
  const canAssignRoles = computed(() => can("users.assignRoles"));
  const canDelete = computed(() => can("users.delete"));

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
  <AdminLayout title="Edit User">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-semibold">Edit user</h1>

      <Button variant="text" :disabled="!canDelete" @click="destroyUser"> Delete </Button>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-2">
      <Card :elevation="1">
        <h2 class="text-lg font-semibold">Details</h2>

        <form class="mt-4 space-y-4" @submit.prevent="updateUser">
          <TextField v-model="userForm.name" label="Name" :state="userForm.errors.name ? 'error' : 'default'" :message="userForm.errors.name" :disabled="!canUpdate" variant="outlined" />

          <TextField v-model="userForm.email" label="Email" type="email" :state="userForm.errors.email ? 'error' : 'default'" :message="userForm.errors.email" :disabled="!canUpdate" variant="outlined" />

          <TextField v-model="userForm.password" label="New password (optional)" type="password" :state="userForm.errors.password ? 'error' : 'default'" :message="userForm.errors.password" :disabled="!canUpdate" variant="filled" />

          <div class="flex justify-end">
            <Button variant="filled" type="submit" :disabled="!canUpdate || userForm.processing"> Save </Button>
          </div>
        </form>
      </Card>

      <Card :elevation="1">
        <div class="flex items-center justify-between">
          <h2 class="text-lg font-semibold">Roles</h2>
          <Button variant="tonal" :disabled="!canAssignRoles || rolesForm.processing" @click="syncRoles"> Update roles </Button>
        </div>

        <div class="mt-4 space-y-2">
          <label v-for="r in roles" :key="r.id" class="flex items-center gap-3 rounded-xl border border-black/5 p-3 dark:border-white/10" :class="!canAssignRoles ? 'opacity-60' : ''">
            <input type="checkbox" class="h-4 w-4" :disabled="!canAssignRoles" :checked="rolesForm.roles.includes(r.name)" @change="toggleRole(r.name)" />
            <span class="text-sm">{{ r.name }}</span>
          </label>
        </div>

        <p v-if="rolesForm.errors.roles" class="mt-2 text-xs opacity-80">
          {{ rolesForm.errors.roles }}
        </p>
      </Card>
    </div>
  </AdminLayout>
</template>
