<script setup lang="ts">
  import { Link, router, useForm, usePage } from "@inertiajs/vue3";
  import { computed, watch } from "vue";
  import { Button } from "@/components/ui/button";
  import { Card } from "@/components/ui/card";
  import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle } from "@/components/ui/dialog";
  import { Input } from "@/components/ui/input";
  import { useAbility } from "@/composables/useAbility";
  import AppLayout from "@/layouts/AppLayout.vue";
  import { dashboard } from "@/routes/admin";
  import { create, destroy, edit, index, store, update } from "@/routes/admin/users";
  import { sync } from "@/routes/admin/users/roles";
  import { type BreadcrumbItem } from "@/types";

  type Role = { id: number; name: string };
  type UserModal = { mode: "create" } | { mode: "edit"; user: { id: number; name: string; email: string }; userRoles: string[] };

  const props = defineProps<{
    users: any;
    modal?: UserModal;
    roles?: Role[];
  }>();

  const { can } = useAbility();
  const page = usePage();

  const isCreate = computed(() => props.modal?.mode === "create");
  const isEdit = computed(() => props.modal?.mode === "edit");
  const isOpen = computed(() => Boolean(props.modal));

  const canCreate = computed(() => can("users.create"));
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
  ];

  const createForm = useForm({
    name: "",
    email: "",
    password: "",
  });

  const editForm = useForm({
    name: "",
    email: "",
    password: "",
  });

  const rolesForm = useForm({
    roles: [] as string[],
  });

  const submitCreate = () => {
    if (!canCreate.value) return;
    createForm.post(store.url());
  };

  const submitEdit = () => {
    if (!canUpdate.value) return;
    if (!props.modal || props.modal.mode !== "edit") return;
    editForm.put(update.url(props.modal.user.id), { preserveScroll: true });
  };

  const syncRoles = () => {
    if (!canAssignRoles.value) return;
    if (!props.modal || props.modal.mode !== "edit") return;
    rolesForm.put(sync.url(props.modal.user.id), { preserveScroll: true });
  };

  const destroyUser = () => {
    if (!canDelete.value) return;
    if (!props.modal || props.modal.mode !== "edit") return;
    if (!confirm("Delete this user? This is not reversible.")) return;
    editForm.delete(destroy.url(props.modal.user.id));
  };

  const closeModal = () => {
    router.visit(index.url(), { preserveScroll: true, preserveState: true });
  };

  const handleOpenChange = (value: boolean) => {
    if (!value) {
      closeModal();
    }
  };

  const toggleRole = (roleName: string) => {
    const idx = rolesForm.roles.indexOf(roleName);
    if (idx >= 0) rolesForm.roles.splice(idx, 1);
    else rolesForm.roles.push(roleName);
  };

  watch(
    () => props.modal,
    (modal) => {
      if (!modal) {
        return;
      }

      if (modal.mode === "create") {
        createForm.reset();
        createForm.clearErrors();
        return;
      }

      editForm.name = modal.user.name;
      editForm.email = modal.user.email;
      editForm.password = "";
      editForm.clearErrors();

      rolesForm.roles = [...modal.userRoles];
      rolesForm.clearErrors();
    },
    { immediate: true },
  );
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-semibold">Users</h1>

      <Button v-if="canCreate" variant="filled" as-child>
        <Link :href="create.url()">New user</Link>
      </Button>
    </div>

    <Card class="mt-6 overflow-hidden py-0">
      <table class="w-full text-sm">
        <thead class="opacity-70">
          <tr>
            <th class="p-3 text-left">Name</th>
            <th class="p-3 text-left">Email</th>
            <th class="p-3 text-right">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="u in users.data" :key="u.id" class="border-t border-black/5 dark:border-white/10">
            <td class="p-3">{{ u.name }}</td>
            <td class="p-3">{{ u.email }}</td>
            <td class="p-3 text-right">
              <Button v-if="canUpdate" variant="text" size="sm" as-child>
                <Link :href="edit.url(u.id)">Edit</Link>
              </Button>

              <form v-if="canDelete" class="inline" method="post" :action="destroy.url(u.id)">
                <input type="hidden" name="_method" value="delete" />
                <input type="hidden" name="_token" :value="page.props.csrf_token" />
                <Button type="submit" variant="text" size="sm">Delete</Button>
              </form>
            </td>
          </tr>
        </tbody>
      </table>
    </Card>

    <Dialog :open="isOpen" @update:open="handleOpenChange">
      <DialogContent class="sm:max-w-3xl">
        <DialogHeader>
          <DialogTitle>{{ isEdit ? "Edit User" : "Create User" }}</DialogTitle>
          <DialogDescription>
            {{ isEdit ? "Update user details and roles." : "Add a new user to the system." }}
          </DialogDescription>
        </DialogHeader>

        <div class="space-y-6">
          <Card class="px-6">
            <h2 class="text-lg font-semibold">Details</h2>

            <form v-if="isEdit" class="mt-4 space-y-4" @submit.prevent="submitEdit">
              <Input id="edit-user-name" v-model="editForm.name" name="name" label="Name" variant="outlined" :disabled="!canUpdate" :state="editForm.errors.name ? 'error' : 'default'" :message="editForm.errors.name" />

              <Input id="edit-user-email" v-model="editForm.email" type="email" name="email" label="Email" variant="outlined" :disabled="!canUpdate" :state="editForm.errors.email ? 'error' : 'default'" :message="editForm.errors.email" />

              <Input id="edit-user-password" v-model="editForm.password" type="password" name="password" label="New password (optional)" variant="outlined" :disabled="!canUpdate" :state="editForm.errors.password ? 'error' : 'default'" :message="editForm.errors.password" />

              <div class="flex items-center justify-between">
                <Button variant="text" :disabled="!canDelete" @click="destroyUser"> Delete </Button>
                <div class="flex flex-1 justify-end">
                  <Button variant="filled" type="submit" :disabled="!canUpdate || editForm.processing"> Save </Button>
                </div>
              </div>
            </form>

            <form v-else class="mt-4 space-y-4" @submit.prevent="submitCreate">
              <Input id="create-user-name" v-model="createForm.name" name="name" label="Name" variant="outlined" :disabled="!canCreate" :state="createForm.errors.name ? 'error' : 'default'" :message="createForm.errors.name" />

              <Input id="create-user-email" v-model="createForm.email" type="email" name="email" label="Email" variant="outlined" :disabled="!canCreate" :state="createForm.errors.email ? 'error' : 'default'" :message="createForm.errors.email" />

              <Input id="create-user-password" v-model="createForm.password" type="password" name="password" label="Password" variant="outlined" :disabled="!canCreate" :state="createForm.errors.password ? 'error' : 'default'" :message="createForm.errors.password" />

              <div class="flex justify-end">
                <Button variant="filled" type="submit" :disabled="!canCreate || createForm.processing"> Create </Button>
              </div>
            </form>
          </Card>

          <Card v-if="isEdit" class="px-6">
            <div class="flex items-center justify-between">
              <h2 class="text-lg font-semibold">Roles</h2>
              <Button variant="tonal" :disabled="!canAssignRoles || rolesForm.processing" @click="syncRoles"> Update roles </Button>
            </div>

            <div class="mt-4 space-y-2">
              <label v-for="r in props.roles ?? []" :key="r.id" class="flex items-center gap-3 rounded-xl border border-black/5 p-3 dark:border-white/10" :class="!canAssignRoles ? 'opacity-60' : ''">
                <input type="checkbox" class="h-4 w-4" :disabled="!canAssignRoles" :checked="rolesForm.roles.includes(r.name)" @change="toggleRole(r.name)" />
                <span class="text-sm">{{ r.name }}</span>
              </label>
            </div>

            <p v-if="rolesForm.errors.roles" class="mt-2 text-xs opacity-80">
              {{ rolesForm.errors.roles }}
            </p>
          </Card>
        </div>
      </DialogContent>
    </Dialog>
  </AppLayout>
</template>
