<script setup lang="ts">
import { Deferred, Head, router, useForm } from '@inertiajs/vue3';
import { computed, watch } from 'vue';
import AdminEditorShell from '@/components/admin/AdminEditorShell.vue';
import AdminPageIntro from '@/components/admin/AdminPageIntro.vue';
import AssignmentTableCard from '@/components/admin/AssignmentTableCard.vue';
import EditPageActionRow from '@/components/admin/EditPageActionRow.vue';
import UserDetailsForm from '@/components/admin/UserDetailsForm.vue';
import UserRoleAssignmentTable from '@/components/admin/UserRoleAssignmentTable.vue';
import Skeleton from '@/components/ui/skeleton/Skeleton.vue';
import { useAbility } from '@/composables/useAbility';
import { useDeleteConfirmation } from '@/composables/useDeleteConfirmation';
import { useSelectionList } from '@/composables/useSelectionList';
import { useSequentialSave } from '@/composables/useSequentialSave';
import { useToast } from '@/composables/useToast';
import { adminPageLayout, setAdminBreadcrumbs } from '@/lib/page-layouts';
import { toTitleCase } from '@/lib/utils';
import { destroy, index, update } from '@/routes/admin/users';
import { sync } from '@/routes/admin/users/roles';
import { adminPermissions } from '@/types/admin-permissions';
import type { AdminUsersEditPageProps } from '@/types/admin/users';
import type { SyncUserRolesRequest, UpdateUserRequest } from '@/types/wayfinder-generated';
defineOptions({
  layout: adminPageLayout,
});

setAdminBreadcrumbs({ title: 'Users', href: index.url() }, { title: 'Edit' });

const props = defineProps<AdminUsersEditPageProps>();

const { can } = useAbility();
const { confirmDelete } = useDeleteConfirmation();
const { success } = useToast();
const userLabel = computed(() => toTitleCase(props.user.name));

const canUpdate = computed(() => can(adminPermissions.usersUpdate));
const canAssignRoles = computed(() => can(adminPermissions.usersAssignRoles));
const canDelete = computed(() => can(adminPermissions.usersDelete));

const userForm = useForm<UpdateUserRequest>({
  name: props.user.name,
  email: props.user.email,
  password: '',
  password_confirmation: '',
});

const rolesForm = useForm<SyncUserRolesRequest>({
  roles: [...props.userRoles],
});
const { replaceSelectedValues, selectedValues: selectedRoles, toggleSelectedValue } = useSelectionList<string>(props.userRoles);
const { createStep, processing: saveProcessing, run } = useSequentialSave();

watch(
  () => props.user,
  (user) => {
    userForm.defaults({
      name: user.name,
      email: user.email,
      password: '',
      password_confirmation: '',
    });

    userForm.name = user.name;
    userForm.email = user.email;
    userForm.reset('password', 'password_confirmation');
  },
  { deep: true },
);

watch(
  () => props.userRoles,
  (userRoles) => {
    rolesForm.defaults({
      roles: [...userRoles],
    });

    replaceSelectedValues(userRoles);
  },
  { deep: true },
);

watch(selectedRoles, (roleNames) => {
  rolesForm.roles = [...roleNames];
});

const detailsDirty = computed(() => canUpdate.value && userForm.isDirty);
const rolesDirty = computed(() => canAssignRoles.value && rolesForm.isDirty);
const isDirty = computed(() => detailsDirty.value || rolesDirty.value);
const actionStatus = computed(() => (isDirty.value ? 'Unsaved changes are ready to save.' : 'No unsaved changes.'));
const actionDescription = computed(() => (isDirty.value ? 'Save the updated account details and access assignments together, then return to the users index.' : 'You can close this editor now, or keep reviewing the account before you leave.'));

const closeToIndex = () => {
  router.visit(index.url());
};

const saveAndClose = async () => {
  const succeeded = await run([
    detailsDirty.value
      ? createStep((callbacks) => {
          userForm.put(update.url(props.user.id, { query: { quiet_success: true } }), {
            only: ['user', 'auth', 'flash'],
            preserveScroll: true,
            onSuccess: () => {
              userForm.defaults({
                name: userForm.name,
                email: userForm.email,
                password: '',
                password_confirmation: '',
              });
              userForm.reset('password', 'password_confirmation');
              callbacks.onSuccess();
            },
            onCancel: callbacks.onCancel,
            onError: callbacks.onError,
            onFinish: callbacks.onFinish,
          });
        })
      : null,
    rolesDirty.value
      ? createStep((callbacks) => {
          rolesForm.roles = [...selectedRoles.value];
          rolesForm.put(sync.url(props.user.id, { query: { quiet_success: true } }), {
            only: ['userRoles', 'flash'],
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

const destroyUser = () => {
  confirmDelete({
    confirmLabel: `Delete ${userLabel.value}`,
    enabled: canDelete.value,
    message: 'Delete this user? This is not reversible.',
    title: `Delete ${userLabel.value}?`,
    onConfirm: () => {
      userForm.delete(destroy.url(props.user.id));
    },
  });
};
</script>

<template>
  <Head :title="`Edit ${userLabel}`" />

  <div id="admin-users-edit-page" class="motion-stage px-4">
    <AdminEditorShell>
      <AdminPageIntro
        id="admin-users-edit-page-header"
        class="motion-step"
        :description="`Update identity details, adjust assigned roles, and keep this account ready for secure handoff.`"
        kicker="User editor"
        style="--motion-order: 0"
        :title="`Edit ${userLabel}`"
      />

      <div id="admin-users-edit-sections" class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_18rem] xl:items-start">
        <UserDetailsForm id="admin-users-edit-details-card" class="motion-step" style="--motion-order: 1" :can-update="canUpdate" :form="userForm" />

        <div id="admin-users-edit-roles-card" class="motion-step xl:col-span-2" style="--motion-order: 2">
          <Deferred data="roles">
            <template #fallback>
              <AssignmentTableCard description="Loading the available roles so you can review and adjust account access without leaving this editor." results-label="Loading roles" title="Role assignments">
                <div class="space-y-4 p-4 md:p-6">
                  <div class="grid gap-3 md:hidden">
                    <div v-for="row in 3" :key="`admin-users-edit-roles-loading-mobile-${row}`" class="rounded-[1.25rem] border border-border/60 bg-background/60 px-4 py-4">
                      <div class="flex items-start gap-4">
                        <Skeleton class="mt-0.5 size-5 rounded-sm" />

                        <div class="min-w-0 flex-1 space-y-2">
                          <Skeleton class="h-4 w-32" />
                          <Skeleton class="h-3 w-24 bg-primary/8" />
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="hidden rounded-[1.25rem] border border-border/60 md:block">
                    <div class="border-b border-border/60 px-6 py-4">
                      <div class="grid gap-4 md:grid-cols-[3.5rem_minmax(0,1.1fr)_minmax(0,0.9fr)]">
                        <Skeleton class="h-4 w-12" />
                        <Skeleton class="h-4 w-32" />
                        <Skeleton class="h-4 w-20" />
                      </div>
                    </div>

                    <div class="space-y-4 px-6 py-5">
                      <div v-for="row in 4" :key="`admin-users-edit-roles-loading-desktop-${row}`" class="grid items-center gap-4 md:grid-cols-[3.5rem_minmax(0,1.1fr)_minmax(0,0.9fr)]">
                        <Skeleton class="h-5 w-5 rounded-sm" />
                        <Skeleton class="h-4 w-40" />
                        <Skeleton class="h-4 w-28" />
                      </div>
                    </div>
                  </div>
                </div>

                <template #footer>
                  <p class="text-xs leading-5 text-muted-foreground">Current role selections are ready. The full role catalog is loading now.</p>
                </template>
              </AssignmentTableCard>
            </template>

            <UserRoleAssignmentTable :can-assign="canAssignRoles" :error="rolesForm.errors.roles" :roles="roles ?? []" :selected-role-names="selectedRoles" @toggle-role="(name, value) => toggleSelectedValue(name, value)" />
          </Deferred>
        </div>

        <aside class="motion-step xl:sticky xl:top-6 xl:col-start-2 xl:row-start-1 xl:self-start" style="--motion-order: 3">
          <EditPageActionRow
            id="admin-users-edit-actions"
            class="xl:ml-auto xl:w-full"
            :can-delete="canDelete"
            :can-save="isDirty"
            close-label="Close"
            :delete-label="`Delete ${userLabel}`"
            :description="actionDescription"
            heading="Finish this account update"
            :processing="saveProcessing"
            save-label="Save and Close"
            :status="actionStatus"
            :status-tone="isDirty ? 'info' : 'muted'"
            @close="closeToIndex"
            @delete="destroyUser"
            @save="saveAndClose"
          />
        </aside>
      </div>
    </AdminEditorShell>
  </div>
</template>
