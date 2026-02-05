<script setup lang="ts">
  import { Link, router, useForm } from "@inertiajs/vue3";
  import { computed, watch } from "vue";
  import { Button } from "@/components/ui/button";
  import { Card } from "@/components/ui/card";
  import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle } from "@/components/ui/dialog";
  import { Input } from "@/components/ui/input";
  import { useAbility } from "@/composables/useAbility";
  import AdminLayout from "@/layouts/AdminLayout.vue";
  import { create, destroy, edit, index, store, update } from "@/routes/admin/permissions";

  type PermissionModal = { mode: "create" } | { mode: "edit"; permission: { id: number; name: string; group: string } };

  const props = defineProps<{
    permissionsByGroup: Record<string, { id: number; name: string; group: string }[]>;
    modal?: PermissionModal;
  }>();

  const { can } = useAbility();
  const canCreate = computed(() => can("permissions.create"));
  const canUpdate = computed(() => can("permissions.update"));
  const canDelete = computed(() => can("permissions.delete"));

  const del = useForm({});
  const form = useForm({
    name: "",
    group: "users",
  });

  const isCreate = computed(() => props.modal?.mode === "create");
  const isEdit = computed(() => props.modal?.mode === "edit");
  const isOpen = computed(() => Boolean(props.modal));

  const destroyPermission = (id: number) => {
    if (!canDelete.value) return;
    if (!confirm("Delete this permission?")) return;
    del.delete(destroy.url(id));
  };

  const submitCreate = () => {
    if (!canCreate.value) return;
    form.post(store.url());
  };

  const submitEdit = () => {
    if (!canUpdate.value) return;
    if (!props.modal || props.modal.mode !== "edit") return;
    form.put(update.url(props.modal.permission.id));
  };

  const destroyFromModal = () => {
    if (!canDelete.value) return;
    if (!props.modal || props.modal.mode !== "edit") return;
    if (!confirm("Delete this permission?")) return;
    form.delete(destroy.url(props.modal.permission.id));
  };

  const closeModal = () => {
    router.visit(index.url(), { preserveScroll: true, preserveState: true });
  };

  const handleOpenChange = (value: boolean) => {
    if (!value) {
      closeModal();
    }
  };

  watch(
    () => props.modal,
    (modal) => {
      if (!modal) {
        return;
      }

      if (modal.mode === "create") {
        form.reset();
        form.clearErrors();
        return;
      }

      form.name = modal.permission.name;
      form.group = modal.permission.group;
      form.clearErrors();
    },
    { immediate: true },
  );
</script>

<template>
  <AdminLayout title="Permissions">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-semibold">Permissions</h1>

      <Button v-if="canCreate" variant="filled" as-child>
        <Link :href="create.url()">New permission</Link>
      </Button>
    </div>

    <div class="mt-6 space-y-6">
      <Card v-for="(items, group) in props.permissionsByGroup" :key="group" class="px-6">
        <div class="flex items-center justify-between">
          <h2 class="text-lg font-semibold capitalize">{{ group }}</h2>
        </div>

        <div class="mt-4 space-y-2">
          <div v-for="p in items" :key="p.id" class="flex items-center justify-between rounded-xl border border-black/5 p-3 dark:border-white/10">
            <div class="text-sm font-medium">{{ p.name }}</div>

            <div class="flex items-center gap-2">
              <Button v-if="canUpdate" variant="text" size="sm" as-child>
                <Link :href="edit.url(p.id)">Edit</Link>
              </Button>

              <Button v-if="canDelete" variant="text" size="sm" @click="destroyPermission(p.id)"> Delete </Button>
            </div>
          </div>
        </div>
      </Card>
    </div>

    <Dialog :open="isOpen" @update:open="handleOpenChange">
      <DialogContent class="sm:max-w-2xl">
        <DialogHeader>
          <DialogTitle>{{ isEdit ? "Edit Permission" : "Create Permission" }}</DialogTitle>
          <DialogDescription>
            {{ isEdit ? "Update permission details." : "Add a new permission to the system." }}
          </DialogDescription>
        </DialogHeader>

        <Card class="px-6">
          <form v-if="isEdit" class="space-y-4" @submit.prevent="submitEdit">
            <Input id="edit-permission-name" v-model="form.name" name="name" label="Permission name" variant="outlined" :disabled="!canUpdate" :state="form.errors.name ? 'error' : 'default'" :message="form.errors.name" />

            <div class="space-y-1">
              <div class="text-sm font-medium opacity-80">Group</div>
              <select v-model="form.group" class="h-14 w-full rounded-[var(--radius-sm)] border border-[color:var(--outline)] bg-[var(--field-bg)] px-4 text-sm text-[var(--field-text)]" :disabled="!canUpdate">
                <option value="users">users</option>
                <option value="roles">roles</option>
                <option value="permissions">permissions</option>
              </select>
              <p v-if="form.errors.group" class="text-xs text-[var(--error)]">
                {{ form.errors.group }}
              </p>
            </div>

            <div class="flex items-center justify-between">
              <Button variant="text" :disabled="!canDelete" @click="destroyFromModal">Delete</Button>
              <div class="flex flex-1 justify-end">
                <Button variant="filled" type="submit" :disabled="!canUpdate || form.processing"> Save </Button>
              </div>
            </div>
          </form>

          <form v-else class="space-y-4" @submit.prevent="submitCreate">
            <Input id="create-permission-name" v-model="form.name" name="name" label="Permission name (e.g. users.view)" variant="outlined" :disabled="!canCreate" :state="form.errors.name ? 'error' : 'default'" :message="form.errors.name" />

            <div class="space-y-1">
              <div class="text-sm font-medium opacity-80">Group</div>
              <select v-model="form.group" class="h-14 w-full rounded-[var(--radius-sm)] border border-[color:var(--outline)] bg-[var(--field-bg)] px-4 text-sm text-[var(--field-text)]" :disabled="!canCreate">
                <option value="users">users</option>
                <option value="roles">roles</option>
                <option value="permissions">permissions</option>
              </select>
              <p v-if="form.errors.group" class="text-xs text-[var(--error)]">
                {{ form.errors.group }}
              </p>
            </div>

            <div class="flex justify-end">
              <Button variant="filled" type="submit" :disabled="!canCreate || form.processing"> Create </Button>
            </div>
          </form>
        </Card>
      </DialogContent>
    </Dialog>
  </AdminLayout>
</template>
