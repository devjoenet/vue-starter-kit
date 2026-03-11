<script setup lang="ts">
import { setLayoutProps, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import Button from '@/components/ui/button/Button.vue';
import Card from '@/components/ui/card/Card.vue';
import Checkbox from '@/components/ui/checkbox/Checkbox.vue';
import Input from '@/components/ui/input/Input.vue';
import { useAbility } from '@/composables/useAbility';
import { useSelectionList } from '@/composables/useSelectionList';
import AppLayout from '@/layouts/AppLayout.vue';
import { toKebabCase, toTitleCase } from '@/lib/utils';
import { dashboard } from '@/routes/admin';
import { create, index, store } from '@/routes/admin/roles';
import { adminPermissions } from '@/types/admin-permissions';
import type { AdminRolesCreatePageProps } from '@/types/page-props';
import type { StoreRoleRequest } from '@/types/wayfinder-generated';
defineOptions({
  layout: AppLayout,
});

setLayoutProps({
  breadcrumbs: [
    { title: 'Dashboard', href: dashboard.url() },
    { title: 'Roles', href: index.url() },
    { title: 'Create', href: create.url() },
  ],
});

const props = defineProps<AdminRolesCreatePageProps>();

const { can } = useAbility();
const canCreate = computed(() => can(adminPermissions.rolesCreate));

const form = useForm<StoreRoleRequest>({
  name: '',
  user_ids: [] as number[],
});
const { hasSelectedValue, selectedValues, toggleSelectedValue } =
  useSelectionList<number>(form.user_ids ?? []);

const normalizeRoleNameForDisplay = () => {
  form.name = toTitleCase(form.name);
};

const submit = () => {
  if (!canCreate.value) return;

  form.user_ids = [...selectedValues.value];
  form
    .transform((data) => ({
      ...data,
      name: toKebabCase(data.name),
    }))
    .post(store.url());
};
</script>

<template>
  <div id="admin-roles-create-page" class="space-y-6 px-4">
    <div
      id="admin-roles-create-page-header"
      class="flex flex-wrap items-center justify-between gap-3"
    >
      <h1 class="text-2xl font-semibold">Create role</h1>
    </div>

    <form
      id="admin-roles-create-form"
      class="space-y-6"
      @submit.prevent="submit"
    >
      <div id="admin-roles-create-sections" class="space-y-6">
        <Card
          id="admin-roles-create-details-card"
          variant="default"
          class="px-6"
        >
          <h2 class="text-lg font-semibold">Role Details</h2>

          <div class="mt-4 space-y-4">
            <Input
              id="create-role-name"
              v-model="form.name"
              name="name"
              label="Role name"
              variant="outlined"
              :disabled="!canCreate"
              :state="form.errors.name ? 'error' : 'default'"
              :message="form.errors.name"
              @blur="normalizeRoleNameForDisplay"
            />
          </div>
        </Card>

        <Card id="admin-roles-create-users-card" variant="default" class="px-6">
          <h2 class="text-lg font-semibold">Assign Users</h2>

          <div class="-mx-3 mt-4 max-h-72 space-y-2 overflow-y-auto px-3">
            <label
              v-for="user in props.users"
              :key="user.id"
              class="flex items-center gap-3 rounded-xl border border-black/5 p-3 dark:border-white/10"
              :class="!canCreate ? 'opacity-60' : ''"
            >
              <Checkbox
                :disabled="!canCreate"
                :model-value="hasSelectedValue(user.id)"
                @update:model-value="
                  (value) => toggleSelectedValue(user.id, value)
                "
              />

              <span class="min-w-0">
                <span class="block truncate text-sm font-medium">{{
                  user.name
                }}</span>
                <span class="block truncate text-xs opacity-70">{{
                  user.email
                }}</span>
              </span>
            </label>
          </div>

          <p v-if="form.errors.user_ids" class="mt-2 text-xs opacity-80">
            {{ form.errors.user_ids }}
          </p>
        </Card>
      </div>

      <div class="flex justify-end">
        <Button
          id="admin-roles-create-submit-button"
          appearance="filled"
          type="submit"
          :disabled="!canCreate || form.processing"
        >
          Create
        </Button>
      </div>
    </form>
  </div>
</template>
