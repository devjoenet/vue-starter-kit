<script setup lang="ts">
  import { useForm } from "@inertiajs/vue3";
  import { computed } from "vue";
  import { Button } from "@/components/ui/button";
  import { Card } from "@/components/ui/card";
  import { Input } from "@/components/ui/input";
  import { useAbility } from "@/composables/useAbility";
  import AppLayout from "@/layouts/AppLayout.vue";
  import { dashboard } from "@/routes/admin";
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
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-semibold">Edit permission</h1>
      <Button variant="text" :disabled="!canDelete" @click="destroyPermission">Delete</Button>
    </div>

    <Card class="mt-6 px-6">
      <form class="space-y-4" @submit.prevent="updatePermission">
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

        <div class="flex justify-end">
          <Button variant="filled" type="submit" :disabled="!canUpdate || form.processing"> Save </Button>
        </div>
      </form>
    </Card>
  </AppLayout>
</template>
