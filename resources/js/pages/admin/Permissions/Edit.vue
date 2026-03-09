<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { h, computed, watch } from 'vue';
import PermissionGroupSelect from '@/components/admin/PermissionGroupSelect.vue';
import Button from '@/components/ui/button/Button.vue';
import Card from '@/components/ui/card/Card.vue';
import Input from '@/components/ui/input/Input.vue';
import { useAbility } from '@/composables/useAbility';
import { useDeleteConfirmation } from '@/composables/useDeleteConfirmation';
import AppLayout from '@/layouts/AppLayout.vue';
import {
  extractPermissionActionSegment,
  normalizePermissionName,
  prefixPermissionWithGroup,
} from '@/lib/permissions';
import { toSnakeCase } from '@/lib/utils';
import { dashboard } from '@/routes/admin';
import { destroy, index, update } from '@/routes/admin/permissions';
import { adminPermissions } from '@/types/admin-permissions';
import type { AdminPermissionsEditPageProps } from '@/types/page-props';
import type { UpdatePermissionRequest } from '@/types/wayfinder-generated';
defineOptions({
  layout: (_: unknown, page: unknown) =>
    h(
      AppLayout,
      {
        breadcrumbs: [
          { title: 'Dashboard', href: dashboard.url() },
          { title: 'Permissions', href: index.url() },
          { title: 'Edit' },
        ],
      },
      () => page,
    ),
});
const props = defineProps<AdminPermissionsEditPageProps>();

const { can } = useAbility();
const { confirmDelete } = useDeleteConfirmation();
const canUpdate = computed(() => can(adminPermissions.permissionsUpdate));
const canDelete = computed(() => can(adminPermissions.permissionsDelete));

const form = useForm<UpdatePermissionRequest>({
  name: props.permission.name,
  group: props.permission.group,
});

watch(
  () => form.group,
  (nextGroup, previousGroup) => {
    const normalizedGroup = toSnakeCase(nextGroup);
    if (normalizedGroup !== nextGroup) {
      form.group = normalizedGroup;
      return;
    }

    const action = extractPermissionActionSegment(
      form.name,
      previousGroup || normalizedGroup,
    );
    form.name = prefixPermissionWithGroup(normalizedGroup, action);
  },
  { immediate: true },
);

const updatePermission = () => {
  if (!canUpdate.value) return;

  form.group = toSnakeCase(form.group);
  form.name = normalizePermissionName(form.name, form.group);
  form.put(update.url(props.permission.id));
};

const destroyPermission = () => {
  confirmDelete({
    enabled: canDelete.value,
    message: 'Delete this permission?',
    onConfirm: () => {
      form.delete(destroy.url(props.permission.id));
    },
  });
};
</script>

<template>
  <div class="space-y-6 px-4">
    <div class="flex flex-wrap items-center justify-between gap-3 pt-12">
      <h1 class="text-2xl font-semibold">Edit permission</h1>
      <Button
        appearance="outline"
        variant="destructive"
        :disabled="!canDelete"
        @click="destroyPermission"
        >Delete</Button
      >
    </div>

    <Card variant="default" class="px-6">
      <form class="space-y-4" @submit.prevent="updatePermission">
        <PermissionGroupSelect
          id="edit-permission-group"
          v-model="form.group"
          :groups="props.groups"
          :disabled="!canUpdate"
          :error="form.errors.group"
        />

        <Input
          id="edit-permission-name"
          v-model="form.name"
          name="name"
          label="Permission name"
          variant="outlined"
          :disabled="!canUpdate"
          :state="form.errors.name ? 'error' : 'default'"
          :message="form.errors.name"
        />

        <div class="flex justify-end">
          <Button
            appearance="filled"
            type="submit"
            :disabled="!canUpdate || form.processing"
          >
            Save
          </Button>
        </div>
      </form>
    </Card>
  </div>
</template>
