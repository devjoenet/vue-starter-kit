<script setup lang="ts">
import { router, setLayoutProps, useForm } from '@inertiajs/vue3';
import { computed, watch } from 'vue';
import PermissionEditorForm from '@/components/admin/PermissionEditorForm.vue';
import { useAbility } from '@/composables/useAbility';
import { useDeleteConfirmation } from '@/composables/useDeleteConfirmation';
import { useToast } from '@/composables/useToast';
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
  layout: AppLayout,
});

setLayoutProps({
  breadcrumbs: [
    { title: 'Dashboard', href: dashboard.url() },
    { title: 'Permissions', href: index.url() },
    { title: 'Edit' },
  ],
});

const props = defineProps<AdminPermissionsEditPageProps>();

const { can } = useAbility();
const { confirmDelete } = useDeleteConfirmation();
const { success } = useToast();
const canUpdate = computed(() => can(adminPermissions.permissionsUpdate));
const canDelete = computed(() => can(adminPermissions.permissionsDelete));
const submitLabel = computed(() => (form.isDirty ? 'Save and Close' : 'Close'));

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

const closeToIndex = () => {
  router.visit(index.url());
};

const updatePermission = () => {
  if (!canUpdate.value) return;

  if (!form.isDirty) {
    closeToIndex();
    return;
  }

  form.group = toSnakeCase(form.group);
  form.name = normalizePermissionName(form.name, form.group);
  form.put(
    update.url(props.permission.id, { query: { quiet_success: true } }),
    {
      preserveScroll: true,
      onSuccess: () => {
        success('Changes saved.');
        closeToIndex();
      },
    },
  );
};

const destroyPermission = () => {
  confirmDelete({
    confirmLabel: 'Delete permission',
    enabled: canDelete.value,
    message: 'Delete this permission? This is not reversible.',
    title: 'Delete permission?',
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
    </div>

    <PermissionEditorForm
      :can-delete="canDelete"
      group-id="edit-permission-group"
      name-id="edit-permission-name"
      :can-submit="canUpdate"
      delete-label="Delete"
      :form="form"
      :groups="props.groups"
      :submit-label="submitLabel"
      @delete="destroyPermission"
      @submit="updatePermission"
    />
  </div>
</template>
