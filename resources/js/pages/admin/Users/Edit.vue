<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { computed, h, watch } from 'vue';
import Button from '@/components/ui/button/Button.vue';
import Card from '@/components/ui/card/Card.vue';
import Checkbox from '@/components/ui/checkbox/Checkbox.vue';
import Input from '@/components/ui/input/Input.vue';
import { useAbility } from '@/composables/useAbility';
import { useDeleteConfirmation } from '@/composables/useDeleteConfirmation';
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
const selectedRoles = computed(() => rolesForm.roles ?? []);

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

    rolesForm.roles = [...userRoles];
  },
  { deep: true },
);

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

const toggleRole = (roleName: string, isChecked: boolean | 'indeterminate') => {
  const nextRoles = new Set(rolesForm.roles ?? []);

  if (isChecked === true) {
    nextRoles.add(roleName);
  } else {
    nextRoles.delete(roleName);
  }

  rolesForm.roles = [...nextRoles];
};
</script>

<template>
  <div class="space-y-6 px-4">
    <div class="flex flex-wrap items-center justify-between gap-3">
      <h1 class="text-2xl font-semibold">
        Edit {{ toTitleCase(props.user.name) }}
      </h1>

      <Button
        appearance="outline"
        variant="destructive"
        :disabled="!canDelete"
        @click="destroyUser"
        >Delete {{ toTitleCase(props.user.name) }}</Button
      >
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
      <Card variant="default" class="px-6">
        <h2 class="text-lg font-semibold">Details</h2>

        <form class="mt-4 space-y-4" @submit.prevent="updateUser">
          <Input
            id="edit-user-name"
            v-model="userForm.name"
            name="name"
            label="Name"
            variant="outlined"
            :disabled="!canUpdate"
            :state="userForm.errors.name ? 'error' : 'default'"
            :message="userForm.errors.name"
          />

          <Input
            id="edit-user-email"
            v-model="userForm.email"
            type="email"
            name="email"
            label="Email"
            variant="outlined"
            :disabled="!canUpdate"
            :state="userForm.errors.email ? 'error' : 'default'"
            :message="userForm.errors.email"
          />

          <Input
            id="edit-user-password"
            v-model="userForm.password"
            type="password"
            name="password"
            label="New password (optional)"
            variant="outlined"
            :disabled="!canUpdate"
            :state="userForm.errors.password ? 'error' : 'default'"
            :message="userForm.errors.password"
          />
          <Input
            id="edit-user-password-confirmation"
            v-model="userForm.password_confirmation"
            type="password"
            name="password_confirmation"
            label="Confirm new password"
            variant="outlined"
            :disabled="!canUpdate"
            :state="userForm.errors.password_confirmation ? 'error' : 'default'"
            :message="userForm.errors.password_confirmation"
          />

          <div class="flex justify-end">
            <Button
              appearance="filled"
              type="submit"
              :disabled="!canUpdate || userForm.processing"
              >Save {{ toTitleCase(props.user.name) }}</Button
            >
          </div>
        </form>
      </Card>

      <Card variant="default" class="px-6">
        <div class="flex items-center justify-between">
          <h2 class="text-lg font-semibold">Roles</h2>
          <Button
            appearance="filled"
            :disabled="!canAssignRoles || rolesForm.processing"
            @click="syncRoles"
            >Save Roles</Button
          >
        </div>

        <div class="-mx-3 mt-4 space-y-2">
          <label
            v-for="r in roles"
            :key="r.id"
            class="flex items-center gap-3 rounded-xl border border-black/5 p-3 dark:border-white/10"
            :class="!canAssignRoles ? 'opacity-60' : ''"
          >
            <Checkbox
              :disabled="!canAssignRoles"
              :model-value="selectedRoles.includes(r.name)"
              @update:model-value="(value) => toggleRole(r.name, value)"
            />
            <span class="text-sm">{{ r.name }}</span>
          </label>
        </div>

        <p v-if="rolesForm.errors.roles" class="mt-2 text-xs opacity-80">
          {{ rolesForm.errors.roles }}
        </p>
      </Card>
    </div>
  </div>
</template>
