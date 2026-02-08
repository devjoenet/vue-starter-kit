<script setup lang="ts">
  import { Link, router, useForm } from "@inertiajs/vue3";
  import { computed, watch } from "vue";
  import { Button } from "@/components/ui/button";
  import { Card } from "@/components/ui/card";
  import { Checkbox } from "@/components/ui/checkbox";
  import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle } from "@/components/ui/dialog";
  import { Input } from "@/components/ui/input";
  import { useAbility } from "@/composables/useAbility";
  import AppLayout from "@/layouts/AppLayout.vue";
  import { dashboard } from "@/routes/admin/index";
  import { create, destroy, edit, index, store, update } from "@/routes/admin/roles";
  import { sync } from "@/routes/admin/roles/permissions";
  import { type BreadcrumbItem } from "@/types";

  type RoleModal = { mode: "create" } | { mode: "edit"; role: { id: number; name: string } };

  const props = defineProps<{
    roles: { id: number; name: string }[];
    modal?: RoleModal;
    permissionsByGroup?: Record<string, { id: number; name: string; group: string }[]>;
    rolePermissions?: string[];
  }>();

  const { can } = useAbility();
  const canCreate = computed(() => can("roles.create"));
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
  ];

  const isCreate = computed(() => props.modal?.mode === "create");
  const isEdit = computed(() => props.modal?.mode === "edit");
  const isOpen = computed(() => Boolean(props.modal));
  const modalKey = computed(() => {
    if (!props.modal) {
      return null;
    }

    if (props.modal.mode === "edit") {
      return `edit:${props.modal.role.id}`;
    }

    return "create";
  });

  const roleForm = useForm({ name: "" });
  const permsForm = useForm({ permissions: [] as string[] });

  const submitCreate = () => {
    if (!canCreate.value) return;
    roleForm.post(store.url());
  };

  const submitEdit = () => {
    if (!canUpdate.value) return;
    if (!props.modal || props.modal.mode !== "edit") return;
    roleForm.put(update.url(props.modal.role.id), { preserveScroll: true });
  };

  const destroyRole = () => {
    if (!canDelete.value) return;
    if (!props.modal || props.modal.mode !== "edit") return;
    if (!confirm("Delete this role?")) return;
    roleForm.delete(destroy.url(props.modal.role.id));
  };

  const syncPermissions = () => {
    if (!canAssign.value) return;
    if (!props.modal || props.modal.mode !== "edit") return;
    permsForm.put(sync.url(props.modal.role.id), { preserveScroll: true });
  };

  const closeModal = () => {
    router.visit(index.url(), { preserveScroll: true, preserveState: true });
  };

  const handleOpenChange = (value: boolean) => {
    if (!value) {
      closeModal();
    }
  };

  const togglePermission = (name: string) => {
    const idx = permsForm.permissions.indexOf(name);
    if (idx >= 0) permsForm.permissions.splice(idx, 1);
    else permsForm.permissions.push(name);
  };

  watch(
    modalKey,
    (key) => {
      if (!key || !props.modal) {
        return;
      }

      if (props.modal.mode === "create") {
        roleForm.reset();
        roleForm.clearErrors();
        permsForm.reset();
        permsForm.clearErrors();
        return;
      }

      roleForm.name = props.modal.role.name;
      roleForm.clearErrors();
      permsForm.permissions = [...(props.rolePermissions ?? [])];
      permsForm.clearErrors();
    },
    { immediate: true },
  );
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6">
      <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-2xl font-semibold">Roles</h1>

        <Button v-if="canCreate" variant="glass" as-child>
          <Link :href="create.url()">New role</Link>
        </Button>
      </div>

      <Card variant="glass" class="px-6">
        <div class="space-y-2 -mx-3">
          <div v-for="r in props.roles" :key="r.id" class="flex items-center justify-between gap-3 rounded-xl border border-black/5 p-3 dark:border-white/10">
            <div class="text-sm font-medium">{{ r.name }}</div>

            <Button v-if="canUpdate" variant="text" size="sm" as-child>
              <Link :href="edit.url(r.id)">Edit</Link>
            </Button>
          </div>
        </div>
      </Card>
    </div>

    <Dialog :open="isOpen" @update:open="handleOpenChange">
      <DialogContent class="sm:max-w-3xl">
        <DialogHeader>
          <DialogTitle>{{ isEdit ? "Edit Role" : "Create Role" }}</DialogTitle>
          <DialogDescription>
            {{ isEdit ? "Update role details and permissions." : "Add a new role to the system." }}
          </DialogDescription>
        </DialogHeader>

        <div class="space-y-6">
          <Card variant="glass" class="px-6">
            <h2 class="text-lg font-semibold">Details</h2>

            <form v-if="isEdit" class="mt-4 space-y-4" @submit.prevent="submitEdit">
              <Input id="edit-role-name" v-model="roleForm.name" name="name" label="Role name" variant="outlined" :disabled="!canUpdate" :state="roleForm.errors.name ? 'error' : 'default'" :message="roleForm.errors.name" />

              <div class="flex items-center justify-between">
                <Button variant="text" :disabled="!canDelete" @click="destroyRole">Delete</Button>
                <div class="flex flex-1 justify-end">
                  <Button variant="filled" type="submit" :disabled="!canUpdate || roleForm.processing"> Save </Button>
                </div>
              </div>
            </form>

            <form v-else class="mt-4 space-y-4" @submit.prevent="submitCreate">
              <Input id="create-role-name" v-model="roleForm.name" name="name" label="Role name" variant="outlined" :disabled="!canCreate" :state="roleForm.errors.name ? 'error' : 'default'" :message="roleForm.errors.name" />

              <div class="flex justify-end">
                <Button variant="filled" type="submit" :disabled="!canCreate || roleForm.processing"> Create </Button>
              </div>
            </form>
          </Card>

          <Card v-if="isEdit" variant="glass" class="px-6">
            <div class="flex items-center justify-between">
              <h2 class="text-lg font-semibold">Permissions</h2>
              <Button variant="tonal" :disabled="!canAssign || permsForm.processing" @click="syncPermissions"> Update permissions </Button>
            </div>

            <div class="mt-4 space-y-5">
              <div v-for="(items, group) in props.permissionsByGroup ?? {}" :key="group" class="space-y-2">
                <div class="text-sm font-semibold capitalize">{{ group }}</div>

                <div class="space-y-2 -mx-3">
                  <label v-for="p in items" :key="p.id" class="flex items-center gap-3 rounded-xl border border-black/5 p-3 dark:border-white/10" :class="!canAssign ? 'opacity-60' : ''">
                    <Checkbox :disabled="!canAssign" :model-value="permsForm.permissions.includes(p.name)" @update:model-value="() => togglePermission(p.name)" />
                    <span class="text-sm">{{ p.name }}</span>
                  </label>
                </div>
              </div>
            </div>
          </Card>
        </div>
      </DialogContent>
    </Dialog>
  </AppLayout>
</template>
