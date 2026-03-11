<script setup lang="ts">
import { router, useForm } from '@inertiajs/vue3';
import { computed, h, watch } from 'vue';
import EditPageActionRow from '@/components/admin/EditPageActionRow.vue';
import RoleDetailsForm from '@/components/admin/RoleDetailsForm.vue';
import RolePermissionAssignmentTable from '@/components/admin/RolePermissionAssignmentTable.vue';
import { useAbility } from '@/composables/useAbility';
import { useDeleteConfirmation } from '@/composables/useDeleteConfirmation';
import { useSelectionList } from '@/composables/useSelectionList';
import { useSequentialSave } from '@/composables/useSequentialSave';
import { useToast } from '@/composables/useToast';
import AppLayout from '@/layouts/AppLayout.vue';
import { toKebabCase, toTitleCase } from '@/lib/utils';
import { dashboard } from '@/routes/admin';
import { destroy, index, update } from '@/routes/admin/roles';
import { sync } from '@/routes/admin/roles/permissions';
import { adminPermissions } from '@/types/admin-permissions';
import type { AdminRolesEditPageProps } from '@/types/page-props';
import type {
  SyncRolePermissionsRequest,
  UpdateRoleRequest,
} from '@/types/wayfinder-generated';
defineOptions({
  layout: (_: unknown, page: unknown) =>
    h(
      AppLayout,
      {
        breadcrumbs: [
          { title: 'Dashboard', href: dashboard.url() },
          { title: 'Roles', href: index.url() },
          { title: 'Edit' },
        ],
      },
      () => page,
    ),
});
const props = defineProps<AdminRolesEditPageProps>();

const { can } = useAbility();
const { confirmDelete } = useDeleteConfirmation();
const { success } = useToast();
const canUpdate = computed(() => can(adminPermissions.rolesUpdate));
const canDelete = computed(() => can(adminPermissions.rolesDelete));
const canAssign = computed(() => can(adminPermissions.rolesAssignPermissions));

const roleForm = useForm<UpdateRoleRequest>({
  name: toTitleCase(props.role.name),
});
const permsForm = useForm<SyncRolePermissionsRequest>({
  permissions: [...props.rolePermissions],
});
const {
  replaceSelectedValues,
  selectedValues: selectedPermissions,
  toggleSelectedValue,
} = useSelectionList<string>(props.rolePermissions);
const { createStep, processing: saveProcessing, run } = useSequentialSave();

watch(
  () => props.role.name,
  (roleName) => {
    roleForm.defaults({
      name: toTitleCase(roleName),
    });

    roleForm.name = toTitleCase(roleName);
  },
  { immediate: true },
);

watch(
  () => props.rolePermissions,
  (permissions) => {
    permsForm.defaults({
      permissions: [...permissions],
    });

    replaceSelectedValues(permissions);
  },
  { immediate: true },
);

watch(selectedPermissions, (permissions) => {
  permsForm.permissions = [...permissions];
});

const detailsDirty = computed(() => canUpdate.value && roleForm.isDirty);
const permissionsDirty = computed(() => canAssign.value && permsForm.isDirty);
const isDirty = computed(() => detailsDirty.value || permissionsDirty.value);
const saveLabel = computed(() => (isDirty.value ? 'Save and Close' : 'Close'));

const closeToIndex = () => {
  router.visit(index.url());
};

const saveOrClose = async () => {
  if (!isDirty.value) {
    closeToIndex();
    return;
  }

  const succeeded = await run([
    detailsDirty.value
      ? createStep((callbacks) => {
          roleForm
            .transform((data) => ({
              ...data,
              name: toKebabCase(data.name),
            }))
            .put(
              update.url(props.role.id, { query: { quiet_success: true } }),
              {
                only: ['role', 'flash'],
                preserveScroll: true,
                onSuccess: () => {
                  roleForm.defaults({
                    name: roleForm.name,
                  });
                  callbacks.onSuccess();
                },
                onCancel: callbacks.onCancel,
                onError: callbacks.onError,
                onFinish: callbacks.onFinish,
              },
            );
        })
      : null,
    permissionsDirty.value
      ? createStep((callbacks) => {
          permsForm.permissions = [...selectedPermissions.value];
          permsForm.put(
            sync.url(props.role.id, { query: { quiet_success: true } }),
            {
              only: ['rolePermissions', 'flash'],
              preserveScroll: true,
              onSuccess: callbacks.onSuccess,
              onCancel: callbacks.onCancel,
              onError: callbacks.onError,
              onFinish: callbacks.onFinish,
            },
          );
        })
      : null,
  ]);

  if (succeeded) {
    success('Changes saved.');
    closeToIndex();
  }
};

const destroyRole = () => {
  confirmDelete({
    confirmLabel: 'Delete role',
    enabled: canDelete.value,
    message: 'Delete this role? This is not reversible.',
    title: `Delete ${toTitleCase(props.role.name)}?`,
    onConfirm: () => {
      router.delete(destroy.url(props.role.id));
    },
  });
};
</script>

<template>
  <div class="space-y-6 px-4">
    <div class="flex flex-wrap items-center justify-between gap-3">
      <h1 class="text-2xl font-semibold">
        Edit {{ toTitleCase(props.role.name) }}
      </h1>
    </div>

    <div class="space-y-6">
      <RoleDetailsForm :can-update="canUpdate" :form="roleForm" />

      <RolePermissionAssignmentTable
        :can-assign="canAssign"
        :error="permsForm.errors.permissions"
        :permissions-by-group="permissionsByGroup"
        :selected-permission-names="selectedPermissions"
        @toggle-permission="(name, value) => toggleSelectedValue(name, value)"
      />

      <EditPageActionRow
        :can-delete="canDelete"
        delete-label="Delete Role"
        :processing="saveProcessing"
        :save-label="saveLabel"
        @delete="destroyRole"
        @save="saveOrClose"
      />
    </div>
  </div>
</template>
