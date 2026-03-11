<script setup lang="ts">
import { router, setLayoutProps, useForm } from '@inertiajs/vue3';
import { computed, watch } from 'vue';
import EditPageActionRow from '@/components/admin/EditPageActionRow.vue';
import UserDetailsForm from '@/components/admin/UserDetailsForm.vue';
import UserRoleAssignmentTable from '@/components/admin/UserRoleAssignmentTable.vue';
import { useAbility } from '@/composables/useAbility';
import { useDeleteConfirmation } from '@/composables/useDeleteConfirmation';
import { useSelectionList } from '@/composables/useSelectionList';
import { useSequentialSave } from '@/composables/useSequentialSave';
import { useToast } from '@/composables/useToast';
import AppLayout from '@/layouts/AppLayout.vue';
import { toTitleCase } from '@/lib/utils';
import { dashboard } from '@/routes/admin';
import { destroy, index, update } from '@/routes/admin/users';
import { sync } from '@/routes/admin/users/roles';
import { adminPermissions } from '@/types/admin-permissions';
import type { AdminUsersEditPageProps } from '@/types/page-props';
import type {
  SyncUserRolesRequest,
  UpdateUserRequest,
} from '@/types/wayfinder-generated';
defineOptions({
  layout: AppLayout,
});

setLayoutProps({
  breadcrumbs: [
    { title: 'Dashboard', href: dashboard.url() },
    { title: 'Users', href: index.url() },
    { title: 'Edit' },
  ],
});

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
const {
  replaceSelectedValues,
  selectedValues: selectedRoles,
  toggleSelectedValue,
} = useSelectionList<string>(props.userRoles);
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
          userForm.put(
            update.url(props.user.id, { query: { quiet_success: true } }),
            {
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
            },
          );
        })
      : null,
    rolesDirty.value
      ? createStep((callbacks) => {
          rolesForm.roles = [...selectedRoles.value];
          rolesForm.put(
            sync.url(props.user.id, { query: { quiet_success: true } }),
            {
              only: ['userRoles', 'flash'],
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
  <div class="space-y-6 px-4">
    <div class="flex flex-wrap items-center justify-between gap-3">
      <h1 class="text-2xl font-semibold">Edit {{ userLabel }}</h1>
    </div>

    <div class="space-y-6">
      <UserDetailsForm :can-update="canUpdate" :form="userForm" />

      <UserRoleAssignmentTable
        :can-assign="canAssignRoles"
        :error="rolesForm.errors.roles"
        :roles="roles"
        :selected-role-names="selectedRoles"
        @toggle-role="(name, value) => toggleSelectedValue(name, value)"
      />

      <EditPageActionRow
        :can-delete="canDelete"
        :delete-label="`Delete ${userLabel}`"
        :processing="saveProcessing"
        :save-label="saveLabel"
        @delete="destroyUser"
        @save="saveOrClose"
      />
    </div>
  </div>
</template>
