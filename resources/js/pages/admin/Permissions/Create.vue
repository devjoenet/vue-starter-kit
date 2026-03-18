<script setup lang="ts">
import { Head, router, setLayoutProps, useForm } from '@inertiajs/vue3';
import { computed, watch } from 'vue';
import AdminPageIntro from '@/components/admin/AdminPageIntro.vue';
import PermissionEditorForm from '@/components/admin/PermissionEditorForm.vue';
import Card from '@/components/ui/card/Card.vue';
import { useAbility } from '@/composables/useAbility';
import AppLayout from '@/layouts/AppLayout.vue';
import {
  extractPermissionActionSegment,
  inferPermissionLabel,
  normalizePermissionName,
  prefixPermissionWithGroup,
} from '@/lib/permissions';
import { toSnakeCase, toTitleCase } from '@/lib/utils';
import { dashboard } from '@/routes/admin';
import { create, index, store } from '@/routes/admin/permissions';
import { adminPermissions } from '@/types/admin-permissions';
import type {
  AdminPermissionsCreatePageProps,
  PermissionGroupOption,
} from '@/types/page-props';
import type { StorePermissionRequest } from '@/types/wayfinder-generated';
defineOptions({
  layout: AppLayout,
});

type PermissionCreateFormData = StorePermissionRequest;

setLayoutProps({
  breadcrumbs: [
    { title: 'Dashboard', href: dashboard.url() },
    { title: 'Permissions', href: index.url() },
    { title: 'Create', href: create.url() },
  ],
});

const props = defineProps<AdminPermissionsCreatePageProps>();

const { can } = useAbility();
const canCreate = computed(() => can(adminPermissions.permissionsCreate));
const groupOptionMap = computed(
  () => new Map(props.groups.map((group) => [group.slug, group] as const)),
);

const resolveGroupOption = (group: string): PermissionGroupOption => {
  const slug = toSnakeCase(group);

  return (
    groupOptionMap.value.get(slug) ?? {
      slug,
      label: toTitleCase(slug),
      description: null,
      permissions_count: 0,
    }
  );
};

const form = useForm<PermissionCreateFormData>({
  name: 'users.view',
  label: 'View',
  description: '',
  group: 'users',
  group_label: resolveGroupOption('users').label,
  group_description: resolveGroupOption('users').description ?? '',
});

watch(
  () => form.group,
  (nextGroup, previousGroup) => {
    const normalizedGroup = toSnakeCase(nextGroup);
    if (normalizedGroup !== nextGroup) {
      form.group = normalizedGroup;
      return;
    }

    const previousOption = resolveGroupOption(previousGroup || normalizedGroup);
    const nextOption = resolveGroupOption(normalizedGroup);
    const previousGroupSlug = toSnakeCase(previousGroup || normalizedGroup);

    if (
      !form.name ||
      !form.name.includes('.') ||
      form.name.startsWith(`${previousGroupSlug}.`)
    ) {
      const action =
        extractPermissionActionSegment(
          form.name || 'view',
          previousGroupSlug || normalizedGroup,
        ) || 'view';

      form.name = prefixPermissionWithGroup(normalizedGroup, action);
    }

    if (
      form.group_label === previousOption.label ||
      !form.group_label?.trim()
    ) {
      form.group_label = nextOption.label;
    }

    if (
      form.group_description === (previousOption.description ?? '') ||
      !form.group_description?.trim()
    ) {
      form.group_description = nextOption.description ?? '';
    }
  },
  { immediate: true },
);

watch(
  () => form.name,
  (nextName, previousName) => {
    const previousLabel = inferPermissionLabel(previousName || '');

    if (form.label === previousLabel || !form.label?.trim()) {
      form.label = inferPermissionLabel(nextName);
    }
  },
);

const submit = () => {
  if (!canCreate.value) return;

  form.group = toSnakeCase(form.group);
  form.name = normalizePermissionName(form.name, form.group);
  form.label = form.label.trim() || inferPermissionLabel(form.name);
  form.group_label =
    form.group_label.trim() || resolveGroupOption(form.group).label;
  form.post(store.url());
};

const closeToIndex = () => {
  router.visit(index.url());
};
</script>

<template>
  <Head title="Create permission" />

  <div id="admin-permissions-create-page" class="motion-stage px-4">
    <section
      class="surface-editor-shell relative overflow-hidden rounded-[1.75rem] px-4 py-6 sm:px-6"
    >
      <div class="relative space-y-6">
        <AdminPageIntro
          id="admin-permissions-create-page-header"
          class="motion-step"
          description="Create a durable permission key, a clear human label, and reusable group metadata that can scale past the starter ACL."
          kicker="Permission editor"
          style="--motion-order: 0"
          title="Create a new permission"
        />

        <div
          class="grid gap-6 xl:grid-cols-[minmax(0,1.18fr)_minmax(18rem,0.82fr)]"
        >
          <PermissionEditorForm
            id="admin-permissions-create-form-card"
            class="motion-step"
            close-label="Back to permissions"
            description="Start with a stable permission key, then describe it clearly for administrators, auditors, and future application modules."
            group-id="create-permission-group"
            group-description-id="create-permission-group-description"
            group-label-id="create-permission-group-label"
            heading="Define the permission and the group it belongs to."
            label-id="create-permission-label"
            name-id="create-permission-name"
            name-supporting-text="Choose a machine key that will stay stable in authorization checks. The key can be prefixed from the selected group, but it is still a first-class catalog identifier."
            permission-description-id="create-permission-description"
            save-description="Create the permission when the key, label, and group metadata all read clearly together, or return to the permissions index without saving."
            save-heading="Create this catalog entry"
            style="--motion-order: 1"
            :can-edit-key="canCreate"
            :can-submit="canCreate"
            :form="form"
            :groups="props.groups"
            :show-save="canCreate"
            submit-label="Create permission"
            @close="closeToIndex"
            @submit="submit"
          />

          <Card
            class="surface-editor-rail motion-step gap-4 px-5 py-5"
            style="--motion-order: 2"
          >
            <p class="section-kicker">Catalog guidance</p>
            <h2 class="text-lg font-semibold tracking-tight">
              Stable keys, human labels, shared groups.
            </h2>
            <p class="text-sm leading-6 text-muted-foreground">
              Treat the permission key like a durable API contract, then use the
              label and descriptions to make the catalog readable for the people
              assigning access. Group metadata should describe the broader
              business area, not a single one-off permission.
            </p>
          </Card>
        </div>
      </div>
    </section>
  </div>
</template>
