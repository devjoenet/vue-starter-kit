<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { h, computed, watch } from 'vue';
import PermissionGroupSelect from '@/components/admin/PermissionGroupSelect.vue';
import Button from '@/components/ui/button/Button.vue';
import Card from '@/components/ui/card/Card.vue';
import Input from '@/components/ui/input/Input.vue';
import { useAbility } from '@/composables/useAbility';
import AppLayout from '@/layouts/AppLayout.vue';
import {
  extractPermissionActionSegment,
  normalizePermissionName,
  prefixPermissionWithGroup,
} from '@/lib/permissions';
import { toSnakeCase } from '@/lib/utils';
import { dashboard } from '@/routes/admin';
import { create, index, store } from '@/routes/admin/permissions';
import { adminPermissions } from '@/types/admin-permissions';
import type { AdminPermissionsCreatePageProps } from '@/types/page-props';
import type { StorePermissionRequest } from '@/types/wayfinder-generated';
defineOptions({
  layout: (_: unknown, page: unknown) =>
    h(
      AppLayout,
      {
        breadcrumbs: [
          { title: 'Dashboard', href: dashboard.url() },
          { title: 'Permissions', href: index.url() },
          { title: 'Create', href: create.url() },
        ],
      },
      () => page,
    ),
});
const props = defineProps<AdminPermissionsCreatePageProps>();

const { can } = useAbility();
const canCreate = computed(() => can(adminPermissions.permissionsCreate));

const form = useForm<StorePermissionRequest>({
  name: 'users.',
  group: 'users',
});

watch(
  () => form.group,
  (nextGroup, previousGroup) => {
    const normalizedGroup = toSnakeCase(nextGroup);
    if (normalizedGroup !== nextGroup) {
      form.group = normalizedGroup;
      return;
    }

    const action = extractPermissionActionSegment(
      form.name,
      previousGroup || normalizedGroup,
    );
    form.name = prefixPermissionWithGroup(normalizedGroup, action);
  },
  { immediate: true },
);

const submit = () => {
  if (!canCreate.value) return;

  form.group = toSnakeCase(form.group);
  form.name = normalizePermissionName(form.name, form.group);
  form.post(store.url());
};
</script>

<template>
  <div class="space-y-6 px-4">
    <div class="flex flex-wrap items-center justify-between gap-3">
      <h1 class="text-2xl font-semibold">Create permission</h1>
    </div>

    <Card variant="default" class="px-6">
      <form class="space-y-4" @submit.prevent="submit">
        <PermissionGroupSelect
          id="create-permission-group"
          v-model="form.group"
          :groups="props.groups"
          :disabled="!canCreate"
          :error="form.errors.group"
        />

        <Input
          id="create-permission-name"
          v-model="form.name"
          name="name"
          label="Permission Name"
          variant="outlined"
          :disabled="!canCreate"
          :state="form.errors.name ? 'error' : 'default'"
          :message="form.errors.name"
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
