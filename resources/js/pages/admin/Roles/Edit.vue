<script setup lang="ts">
import { router, useForm } from '@inertiajs/vue3';
import { computed, h, ref, watch } from 'vue';
import RoleDetailsForm from '@/components/admin/RoleDetailsForm.vue';
import RolePermissionAssignmentTable from '@/components/admin/RolePermissionAssignmentTable.vue';
import Button from '@/components/ui/button/Button.vue';
import { useAbility } from '@/composables/useAbility';
import { useDeleteConfirmation } from '@/composables/useDeleteConfirmation';
import { useSelectionList } from '@/composables/useSelectionList';
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
const canUpdate = computed(() => can(adminPermissions.rolesUpdate));
const canDelete = computed(() => can(adminPermissions.rolesDelete));
const canAssign = computed(() => can(adminPermissions.rolesAssignPermissions));

const roleForm = useForm<UpdateRoleRequest>({
  name: props.role.name,
});
const permsForm = useForm<SyncRolePermissionsRequest>({
  permissions: [...props.rolePermissions],
});
const permissionsSyncInFlight = ref(false);
const {
  replaceSelectedValues,
  selectedValues: selectedPermissions,
  toggleSelectedValue,
} = useSelectionList<string>(props.rolePermissions);

watch(
  () => props.role.name,
  (roleName) => {
    roleForm.name = roleName;
  },
  { immediate: true },
);

watch(
  () => props.rolePermissions,
  (permissions) => {
    replaceSelectedValues(permissions);
  },
  { immediate: true },
);

watch(selectedPermissions, (permissions) => {
  permsForm.permissions = [...permissions];
});

const updateRole = () => {
  if (!canUpdate.value) return;

  roleForm.name = toKebabCase(roleForm.name);
  roleForm.put(update.url(props.role.id), {
    only: ['role', 'flash'],
    preserveScroll: true,
  });
};

const syncPermissions = () => {
  if (!canAssign.value) return;

  permsForm.permissions = [...selectedPermissions.value];
  permsForm.put(sync.url(props.role.id), {
    only: ['rolePermissions', 'flash'],
    preserveScroll: true,
    onStart: () => {
      permissionsSyncInFlight.value = true;
    },
    onFinish: () => {
      permissionsSyncInFlight.value = false;
    },
  });
};

const destroyRole = () => {
  confirmDelete({
    enabled: canDelete.value,
    message: 'Delete this role?',
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
      <Button
        appearance="outline"
        variant="destructive"
        :disabled="!canDelete"
        @click="destroyRole"
        >Delete Role</Button
      >
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
      <RoleDetailsForm
        :can-update="canUpdate"
        :form="roleForm"
        @submit="updateRole"
      />

      <RolePermissionAssignmentTable
        :can-assign="canAssign"
        :error="permsForm.errors.permissions"
        :permissions-by-group="permissionsByGroup"
        :processing="permissionsSyncInFlight"
        :selected-permission-names="selectedPermissions"
        @save="syncPermissions"
        @toggle-permission="(name, value) => toggleSelectedValue(name, value)"
      />
    </div>
  </div>
</template>
