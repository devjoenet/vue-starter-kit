<script setup lang="ts">
import { Head, router, setLayoutProps, useForm } from '@inertiajs/vue3';
import { computed, watch } from 'vue';
import AdminPageIntro from '@/components/admin/AdminPageIntro.vue';
import EditPageActionRow from '@/components/admin/EditPageActionRow.vue';
import RoleDetailsForm from '@/components/admin/RoleDetailsForm.vue';
import RolePermissionAssignmentTable from '@/components/admin/RolePermissionAssignmentTable.vue';
import Badge from '@/components/ui/badge/Badge.vue';
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
import type { SyncRolePermissionsRequest, UpdateRoleRequest } from '@/types/wayfinder-generated';
defineOptions({
  layout: AppLayout,
});

setLayoutProps({
  breadcrumbs: [{ title: 'Dashboard', href: dashboard.url() }, { title: 'Roles', href: index.url() }, { title: 'Edit' }],
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
const { replaceSelectedValues, selectedValues: selectedPermissions, toggleSelectedValue } = useSelectionList<string>(props.rolePermissions);
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
const permissionGroupCount = computed(() => Object.keys(props.permissionsByGroup).length);
const actionStatus = computed(() => (isDirty.value ? 'Unsaved changes are ready to save.' : 'No unsaved changes.'));
const actionDescription = computed(() => (isDirty.value ? 'Save the role details and permission assignments together, then return to the roles index.' : 'You can close this editor now, or keep reviewing the current permission footprint.'));

const closeToIndex = () => {
  router.visit(index.url());
};

const saveAndClose = async () => {
  const succeeded = await run([
    detailsDirty.value
      ? createStep((callbacks) => {
          roleForm
            .transform((data) => ({
              ...data,
              name: toKebabCase(data.name),
            }))
            .put(update.url(props.role.id, { query: { quiet_success: true } }), {
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
            });
        })
      : null,
    permissionsDirty.value
      ? createStep((callbacks) => {
          permsForm.permissions = [...selectedPermissions.value];
          permsForm.put(sync.url(props.role.id, { query: { quiet_success: true } }), {
            only: ['rolePermissions', 'flash'],
            preserveScroll: true,
            onSuccess: callbacks.onSuccess,
            onCancel: callbacks.onCancel,
            onError: callbacks.onError,
            onFinish: callbacks.onFinish,
          });
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
  <Head :title="`Edit ${toTitleCase(props.role.name)}`" />

  <div id="admin-roles-edit-page" class="motion-stage px-4">
    <section class="surface-editor-shell relative overflow-hidden rounded-[1.75rem] px-4 py-6 sm:px-6">
      <div class="relative space-y-6">
        <AdminPageIntro
          id="admin-roles-edit-page-header"
          class="motion-step"
          :description="`Adjust the role name and permission footprint together so access reviews stay clean and easy to explain.`"
          kicker="Role editor"
          style="--motion-order: 0"
          :title="`Edit ${toTitleCase(props.role.name)}`"
        >
          <template #aside>
            <Badge variant="secondary"> {{ selectedPermissions.length }} permission{{ selectedPermissions.length === 1 ? '' : 's' }} </Badge>
            <Badge variant="info"> {{ permissionGroupCount }} group{{ permissionGroupCount === 1 ? '' : 's' }} </Badge>
          </template>
        </AdminPageIntro>

        <div id="admin-roles-edit-sections" class="grid gap-6 xl:grid-cols-[minmax(0,1.18fr)_minmax(18rem,0.82fr)]">
          <div class="space-y-6">
            <RoleDetailsForm id="admin-roles-edit-details-card" class="motion-step" style="--motion-order: 1" :can-update="canUpdate" :form="roleForm" />

            <RolePermissionAssignmentTable
              id="admin-roles-edit-permissions-card"
              class="motion-step"
              style="--motion-order: 2"
              :can-assign="canAssign"
              :error="permsForm.errors.permissions"
              :permissions-by-group="permissionsByGroup"
              :selected-permission-names="selectedPermissions"
              @toggle-permission="(name, value) => toggleSelectedValue(name, value)"
            />
          </div>

          <aside class="motion-step xl:sticky xl:top-6 xl:self-start" style="--motion-order: 3">
            <EditPageActionRow
              id="admin-roles-edit-actions"
              :can-delete="canDelete"
              :can-save="isDirty"
              close-label="Close"
              delete-label="Delete Role"
              :description="actionDescription"
              heading="Finish this role update"
              :processing="saveProcessing"
              save-label="Save and Close"
              :status="actionStatus"
              :status-tone="isDirty ? 'info' : 'muted'"
              @close="closeToIndex"
              @delete="destroyRole"
              @save="saveAndClose"
            />
          </aside>
        </div>
      </div>
    </section>
  </div>
</template>
