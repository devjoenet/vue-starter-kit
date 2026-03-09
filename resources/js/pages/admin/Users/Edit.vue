<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { computed, h, watch } from 'vue';
import UserDetailsForm from '@/components/admin/UserDetailsForm.vue';
import UserRoleAssignmentTable from '@/components/admin/UserRoleAssignmentTable.vue';
import Button from '@/components/ui/button/Button.vue';
import { useAbility } from '@/composables/useAbility';
import { useDeleteConfirmation } from '@/composables/useDeleteConfirmation';
import { useSelectionList } from '@/composables/useSelectionList';
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
  layout: (_: unknown, page: unknown) =>
    h(
      AppLayout,
      {
        breadcrumbs: [
          { title: 'Dashboard', href: dashboard.url() },
          { title: 'Users', href: index.url() },
          { title: 'Edit' },
        ],
      },
      () => page,
    ),
});

const props = defineProps<AdminUsersEditPageProps>();

const { can } = useAbility();
const { confirmDelete } = useDeleteConfirmation();
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

watch(
  () => props.user,
  (user) => {
    userForm.defaults({
      name: user.name,
      email: user.email,
      password: '',
      password_confirmation: '',
    });
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

const updateUser = () => {
  if (!canUpdate.value) return;

  userForm.put(update.url(props.user.id), {
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
    },
  });
};

const syncRoles = () => {
  if (!canAssignRoles.value) return;

  rolesForm.put(sync.url(props.user.id), {
    only: ['userRoles', 'flash'],
    preserveScroll: true,
  });
};

const destroyUser = () => {
  confirmDelete({
    enabled: canDelete.value,
    message: 'Delete this user? This is not reversible.',
    onConfirm: () => {
      userForm.delete(destroy.url(props.user.id));
    },
  });
};

</script>

<template>
  <div class="space-y-6 px-4">
    <div class="flex flex-wrap items-center justify-between gap-3">
      <h1 class="text-2xl font-semibold">
        Edit {{ userLabel }}
      </h1>

      <Button
        appearance="outline"
        variant="destructive"
        :disabled="!canDelete"
        @click="destroyUser"
        >Delete {{ userLabel }}</Button
      >
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
      <UserDetailsForm
        :can-update="canUpdate"
        :form="userForm"
        :user-label="userLabel"
        @submit="updateUser"
      />

      <UserRoleAssignmentTable
        :can-assign="canAssignRoles"
        :error="rolesForm.errors.roles"
        :processing="rolesForm.processing"
        :roles="roles"
        :selected-role-names="selectedRoles"
        @save="syncRoles"
        @toggle-role="(name, value) => toggleSelectedValue(name, value)"
      />
    </div>
  </div>
</template>
