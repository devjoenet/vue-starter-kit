<script setup lang="ts">
  import { useForm } from "@inertiajs/vue3";
  import { computed } from "vue";
  import PermissionGroupSelect from "@/components/admin/PermissionGroupSelect.vue";
  import { Button } from "@/components/ui/button";
  import { Card } from "@/components/ui/card";
  import { Input } from "@/components/ui/input";
  import { useAbility } from "@/composables/useAbility";
  import AppLayout from "@/layouts/AppLayout.vue";
  import { dashboard } from "@/routes/admin/index";
  import { destroy, edit, index, update } from "@/routes/admin/permissions";
  import { type BreadcrumbItem } from "@/types";

  const props = defineProps<{
    permission: { id: number; name: string; group: string };
  }>();

  const { can } = useAbility();
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
    {
      title: "Edit",
      href: edit.url(props.permission.id),
    },
  ];

  const form = useForm({
    name: props.permission.name,
    group: props.permission.group,
  });

  const updatePermission = () => {
    if (!canUpdate.value) return;
    form.put(update.url(props.permission.id));
  };

  const destroyPermission = () => {
    if (!canDelete.value) return;
    if (!confirm("Delete this permission?")) return;
    form.delete(destroy.url(props.permission.id));
  };
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 px-4">
      <div class="flex flex-wrap items-center justify-between gap-3 pt-12">
        <h1 class="text-2xl font-semibold">Edit permission</h1>
        <Button appearance="text" :disabled="!canDelete" @click="destroyPermission">Delete</Button>
      </div>

      <Card variant="glass" class="px-6">
        <form class="space-y-4" @submit.prevent="updatePermission">
          <Input id="edit-permission-name" v-model="form.name" name="name" label="Permission name" variant="outlined" :disabled="!canUpdate" :state="form.errors.name ? 'error' : 'default'" :message="form.errors.name" />

          <PermissionGroupSelect id="edit-permission-group" v-model="form.group" :disabled="!canUpdate" :error="form.errors.group" />

          <div class="flex justify-end">
            <Button appearance="filled" type="submit" :disabled="!canUpdate || form.processing"> Save </Button>
          </div>
        </form>
      </Card>
    </div>
  </AppLayout>
</template>
