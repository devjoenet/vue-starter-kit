<script setup lang="ts">
import { Deferred, Head, router, useForm } from '@inertiajs/vue3';
import { computed, watch } from 'vue';
import AdminEditorShell from '@/components/admin/AdminEditorShell.vue';
import AdminPageIntro from '@/components/admin/AdminPageIntro.vue';
import AssignmentTableCard from '@/components/admin/AssignmentTableCard.vue';
import AuditHistoryTable from '@/components/admin/AuditHistoryTable.vue';
import EditPageActionRow from '@/components/admin/EditPageActionRow.vue';
import RoleDetailsForm from '@/components/admin/RoleDetailsForm.vue';
import RolePermissionAssignmentTable from '@/components/admin/RolePermissionAssignmentTable.vue';
import Badge from '@/components/ui/badge/Badge.vue';
import Skeleton from '@/components/ui/skeleton/Skeleton.vue';
import { useAbility } from '@/composables/useAbility';
import { useDeleteConfirmation } from '@/composables/useDeleteConfirmation';
import { useSelectionList } from '@/composables/useSelectionList';
import { useSequentialSave } from '@/composables/useSequentialSave';
import { useToast } from '@/composables/useToast';
import { adminPageLayout, setAdminBreadcrumbs } from '@/lib/page-layouts';
import { toKebabCase, toTitleCase } from '@/lib/utils';
import { destroy, index, update } from '@/routes/admin/roles';
import { sync } from '@/routes/admin/roles/permissions';
import { adminPermissions } from '@/types/admin-permissions';
import type { AdminRolesEditPageProps } from '@/types/admin/roles';
import type { SyncRolePermissionsRequest, UpdateRoleRequest } from '@/types/wayfinder-generated';
defineOptions({
  layout: adminPageLayout,
});

setAdminBreadcrumbs({ title: 'Roles', href: index.url() }, { title: 'Edit' });

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
const permissionGroupCount = computed<number | null>(() => (props.permissionsByGroup ? Object.keys(props.permissionsByGroup).length : null));
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
    <AdminEditorShell>
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
          <Badge variant="info">
            {{ permissionGroupCount === null ? 'Loading groups' : `${permissionGroupCount} group${permissionGroupCount === 1 ? '' : 's'}` }}
          </Badge>
        </template>
      </AdminPageIntro>

      <div id="admin-roles-edit-sections" class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_18rem] xl:items-start">
        <RoleDetailsForm id="admin-roles-edit-details-card" class="motion-step" style="--motion-order: 1" :can-update="canUpdate" :form="roleForm" />

        <div id="admin-roles-edit-permissions-card" class="motion-step xl:col-span-2" style="--motion-order: 2">
          <Deferred data="permissionsByGroup">
            <template #fallback>
              <AssignmentTableCard description="Loading the permission catalog so you can review and adjust this role's access footprint without leaving the editor." results-label="Loading permissions" title="Permission assignments">
                <div class="space-y-4 p-4 md:p-6">
                  <div class="grid gap-3 md:hidden">
                    <div v-for="row in 3" :key="`admin-roles-edit-permissions-loading-mobile-${row}`" class="rounded-[1.25rem] border border-border/60 bg-background/60 px-4 py-4">
                      <div class="flex items-start gap-4">
                        <Skeleton class="mt-0.5 size-5 rounded-sm" />

                        <div class="min-w-0 flex-1 space-y-2">
                          <Skeleton class="h-4 w-36" />
                          <Skeleton class="h-3 w-48 bg-primary/8" />
                          <Skeleton class="h-3 w-28" />
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="hidden rounded-[1.25rem] border border-border/60 md:block">
                    <div class="border-b border-border/60 px-6 py-4">
                      <div class="grid gap-4 md:grid-cols-[3.5rem_minmax(0,0.95fr)_minmax(0,1.1fr)_minmax(0,0.85fr)]">
                        <Skeleton class="h-4 w-12" />
                        <Skeleton class="h-4 w-20" />
                        <Skeleton class="h-4 w-32" />
                        <Skeleton class="h-4 w-28" />
                      </div>
                    </div>

                    <div class="space-y-4 px-6 py-5">
                      <div v-for="row in 5" :key="`admin-roles-edit-permissions-loading-desktop-${row}`" class="grid items-center gap-4 md:grid-cols-[3.5rem_minmax(0,0.95fr)_minmax(0,1.1fr)_minmax(0,0.85fr)]">
                        <Skeleton class="h-5 w-5 rounded-sm" />
                        <Skeleton class="h-4 w-28" />
                        <Skeleton class="h-4 w-44" />
                        <Skeleton class="h-4 w-32" />
                      </div>
                    </div>
                  </div>
                </div>

                <template #footer>
                  <p class="text-xs leading-5 text-muted-foreground">Current permission selections are ready. The full permission catalog is loading now.</p>
                </template>
              </AssignmentTableCard>
            </template>

            <RolePermissionAssignmentTable
              :can-assign="canAssign"
              :error="permsForm.errors.permissions"
              :permissions-by-group="permissionsByGroup ?? {}"
              :selected-permission-names="selectedPermissions"
              @toggle-permission="(name, value) => toggleSelectedValue(name, value)"
            />
          </Deferred>
        </div>

        <aside class="motion-step xl:sticky xl:top-6 xl:col-start-2 xl:row-start-1 xl:self-start" style="--motion-order: 3">
          <EditPageActionRow
            id="admin-roles-edit-actions"
            class="xl:ml-auto xl:w-full"
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

      <div class="motion-step" style="--motion-order: 4">
        <Deferred data="auditHistory">
          <template #fallback>
            <AuditHistoryTable :items="[]" loading />
          </template>

          <AuditHistoryTable :items="auditHistory ?? []" />
        </Deferred>
      </div>
    </AdminEditorShell>
  </div>
</template>
