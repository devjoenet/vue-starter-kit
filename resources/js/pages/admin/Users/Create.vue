<script setup lang="ts">
import { setLayoutProps, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import UserIdentityFields from '@/components/UserIdentityFields.vue';
import Button from '@/components/ui/button/Button.vue';
import Card from '@/components/ui/card/Card.vue';
import Input from '@/components/ui/input/Input.vue';
import { useAbility } from '@/composables/useAbility';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes/admin';
import { create, index, store } from '@/routes/admin/users';
import { adminPermissions } from '@/types/admin-permissions';
import type { AdminUsersCreatePageProps } from '@/types/page-props';
import type { StoreUserRequest } from '@/types/wayfinder-generated';
defineOptions({
  layout: AppLayout,
});

setLayoutProps({
  breadcrumbs: [
    { title: 'Dashboard', href: dashboard.url() },
    { title: 'Users', href: index.url() },
    { title: 'Create', href: create.url() },
  ],
});

defineProps<AdminUsersCreatePageProps>();

const { can } = useAbility();
const canCreate = computed(() => can(adminPermissions.usersCreate));

const form = useForm<StoreUserRequest>({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
});

const submit = () => {
  if (!canCreate.value) return;
  form.post(store.url());
};
</script>

<template>
  <div class="space-y-6 px-4">
    <div class="flex flex-wrap items-center justify-between gap-3">
      <h1 class="text-2xl font-semibold">Create user</h1>
    </div>

    <Card variant="default" class="px-6">
      <form class="space-y-4" @submit.prevent="submit">
        <UserIdentityFields
          name-id="create-user-name"
          email-id="create-user-email"
          v-model:name="form.name"
          v-model:email="form.email"
          :disabled="!canCreate"
          :name-error="form.errors.name"
          :email-error="form.errors.email"
        />

        <Input
          id="create-user-password"
          v-model="form.password"
          type="password"
          name="password"
          label="Password"
          variant="outlined"
          :disabled="!canCreate"
          :state="form.errors.password ? 'error' : 'default'"
          :message="form.errors.password"
        />
        <Input
          id="create-user-password-confirmation"
          v-model="form.password_confirmation"
          type="password"
          name="password_confirmation"
          label="Confirm password"
          variant="outlined"
          :disabled="!canCreate"
          :state="form.errors.password_confirmation ? 'error' : 'default'"
          :message="form.errors.password_confirmation"
        />

        <div class="flex justify-end">
          <Button
            appearance="filled"
            type="submit"
            :disabled="!canCreate || form.processing"
          >
            Create
          </Button>
        </div>
      </form>
    </Card>
  </div>
</template>
