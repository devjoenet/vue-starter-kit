<script setup lang="ts">
  import { useForm } from "@inertiajs/vue3";
  import { computed, watch } from "vue";
  import PermissionGroupSelect from "@/components/admin/PermissionGroupSelect.vue";
  import { Button } from "@/components/ui/button";
  import { Card } from "@/components/ui/card";
  import { Input } from "@/components/ui/input";
  import { useAbility } from "@/composables/useAbility";
  import AppLayout from "@/layouts/AppLayout.vue";
  import { toCamelCase, toSnakeCase } from "@/lib/utils";
  import { dashboard } from "@/routes/admin/index";
  import { destroy, edit, index, update } from "@/routes/admin/permissions";
  import { type BreadcrumbItem } from "@/types";

  const props = defineProps<{
    permission: { id: number; name: string; group: string };
    groups: string[];
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

  const extractActionSegment = (permissionName: string, group: string) => {
    const normalizedGroup = toSnakeCase(group);
    const rawValue = permissionName.trim();

    if (!rawValue) {
      return "";
    }

    if (rawValue.startsWith(`${normalizedGroup}.`)) {
      return rawValue.slice(normalizedGroup.length + 1);
    }

    const segments = rawValue
      .split(".")
      .map((segment) => segment.trim())
      .filter(Boolean);
    if (segments.length > 1) {
      return segments[segments.length - 1];
    }

    return rawValue;
  };

  const prefixWithGroup = (group: string, actionSegment = "") => {
    const normalizedGroup = toSnakeCase(group);
    const normalizedAction = toCamelCase(actionSegment);
    return normalizedAction ? `${normalizedGroup}.${normalizedAction}` : `${normalizedGroup}.`;
  };

  const form = useForm({
    name: props.permission.name,
    group: props.permission.group,
  });

  const normalizePermissionInput = (permissionName: string) => {
    const action = extractActionSegment(permissionName, form.group);
    return prefixWithGroup(form.group, action);
  };

  watch(
    () => form.group,
    (nextGroup, previousGroup) => {
      const normalizedGroup = toSnakeCase(nextGroup);
      if (normalizedGroup !== nextGroup) {
        form.group = normalizedGroup;
        return;
      }

      const action = extractActionSegment(form.name, previousGroup || normalizedGroup);
      form.name = prefixWithGroup(normalizedGroup, action);
    },
    { immediate: true },
  );

  const updatePermission = () => {
    if (!canUpdate.value) return;

    form.group = toSnakeCase(form.group);
    form.name = normalizePermissionInput(form.name);
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
        <Button appearance="outline" variant="destructive" :disabled="!canDelete" @click="destroyPermission">Delete</Button>
      </div>

      <Card variant="default" class="px-6">
        <form class="space-y-4" @submit.prevent="updatePermission">
          <PermissionGroupSelect id="edit-permission-group" v-model="form.group" :groups="props.groups" :disabled="!canUpdate" :error="form.errors.group" />

          <Input id="edit-permission-name" v-model="form.name" name="name" label="Permission name" variant="outlined" :disabled="!canUpdate" :state="form.errors.name ? 'error' : 'default'" :message="form.errors.name" />

          <div class="flex justify-end">
            <Button appearance="filled" type="submit" :disabled="!canUpdate || form.processing"> Save </Button>
          </div>
        </form>
      </Card>
    </div>
  </AppLayout>
</template>
