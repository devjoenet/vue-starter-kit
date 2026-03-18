<script setup lang="ts">
import { Head, router, setLayoutProps, useForm } from '@inertiajs/vue3';
import { computed, watch } from 'vue';
import AdminPageIntro from '@/components/admin/AdminPageIntro.vue';
import PermissionEditorForm from '@/components/admin/PermissionEditorForm.vue';
import Badge from '@/components/ui/badge/Badge.vue';
import Card from '@/components/ui/card/Card.vue';
import { useAbility } from '@/composables/useAbility';
import { useDeleteConfirmation } from '@/composables/useDeleteConfirmation';
import { useToast } from '@/composables/useToast';
import AppLayout from '@/layouts/AppLayout.vue';
import { normalizePermissionName } from '@/lib/permissions';
import { toSnakeCase, toTitleCase } from '@/lib/utils';
import { dashboard } from '@/routes/admin';
import { destroy, index, update } from '@/routes/admin/permissions';
import { adminPermissions } from '@/types/admin-permissions';
import type { AdminPermissionsEditPageProps, PermissionGroupOption } from '@/types/page-props';
import type { UpdatePermissionRequest } from '@/types/wayfinder-generated';
defineOptions({
  layout: AppLayout,
});

type PermissionEditFormData = Omit<UpdatePermissionRequest, 'name'> & {
  name: string;
};

setLayoutProps({
  breadcrumbs: [{ title: 'Dashboard', href: dashboard.url() }, { title: 'Permissions', href: index.url() }, { title: 'Edit' }],
});

const props = defineProps<AdminPermissionsEditPageProps>();

const { can } = useAbility();
const { confirmDelete } = useDeleteConfirmation();
const { success } = useToast();
const canUpdate = computed(() => can(adminPermissions.permissionsUpdate));
const canDelete = computed(() => can(adminPermissions.permissionsDelete));
const groupOptionMap = computed(() => new Map(props.groups.map((group) => [group.slug, group] as const)));
const actionStatus = computed(() => (form.isDirty ? 'Unsaved changes are ready to save.' : 'No unsaved changes.'));
const selectedGroupLabel = computed(() => form.group_label || toTitleCase(form.group));

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

const form = useForm<PermissionEditFormData>({
  name: props.permission.name,
  group: props.permission.group,
  label: props.permission.label,
  description: props.permission.description ?? '',
  group_label: props.permission.group_label,
  group_description: props.permission.group_description ?? '',
});

watch(
  () => form.group,
  (nextGroup, previousGroup = nextGroup) => {
    const normalizedGroup = toSnakeCase(nextGroup);
    if (normalizedGroup !== nextGroup) {
      form.group = normalizedGroup;
      return;
    }

    const previousOption = resolveGroupOption(previousGroup);
    const nextOption = resolveGroupOption(normalizedGroup);

    if (form.group_label === previousOption.label || !form.group_label?.trim()) {
      form.group_label = nextOption.label;
    }

    if (form.group_description === (previousOption.description ?? '') || !form.group_description?.trim()) {
      form.group_description = nextOption.description ?? '';
    }
  },
  { immediate: true },
);

const closeToIndex = () => {
  router.visit(index.url());
};

const updatePermission = () => {
  if (!canUpdate.value) return;

  if (!form.isDirty) {
    closeToIndex();
    return;
  }

  form.name = normalizePermissionName(form.name, form.group);
  form.group = toSnakeCase(form.group);
  form.group_label = form.group_label.trim() || resolveGroupOption(form.group).label;
  form.put(update.url(props.permission.id, { query: { quiet_success: true } }), {
    only: ['permission', 'flash'],
    preserveScroll: true,
    onSuccess: () => {
      success('Changes saved.');
      closeToIndex();
    },
  });
};

const destroyPermission = () => {
  confirmDelete({
    confirmLabel: 'Delete permission',
    enabled: canDelete.value,
    message: 'Delete this permission? This is not reversible.',
    title: 'Delete permission?',
    onConfirm: () => {
      form.delete(destroy.url(props.permission.id));
    },
  });
};
</script>

<template>
  <Head title="Edit permission" />

  <div id="admin-permissions-edit-page" class="motion-stage px-4">
    <section class="surface-editor-shell relative overflow-hidden rounded-[1.75rem] px-4 py-6 sm:px-6">
      <div class="relative space-y-6">
        <AdminPageIntro
          id="admin-permissions-edit-page-header"
          class="motion-step"
          description="Refine the catalog entry without changing the underlying permission key that your authorization checks depend on."
          kicker="Permission editor"
          style="--motion-order: 0"
          title="Edit permission"
        >
          <template #aside>
            <Badge variant="secondary">{{ selectedGroupLabel }}</Badge>
          </template>
        </AdminPageIntro>

        <div class="grid gap-6 xl:grid-cols-[minmax(0,1.18fr)_minmax(18rem,0.82fr)]">
          <PermissionEditorForm
            id="admin-permissions-edit-form-card"
            class="motion-step"
            :can-delete="canDelete"
            close-label="Close"
            description="The permission key stays fixed after creation, but the human label, description, and shared group metadata can evolve with the application."
            delete-label="Delete permission"
            group-id="edit-permission-group"
            group-description-id="edit-permission-group-description"
            group-label-id="edit-permission-group-label"
            heading="Maintain the catalog entry without breaking the key."
            label-id="edit-permission-label"
            name-id="edit-permission-name"
            name-readonly
            name-supporting-text="This machine key is locked after creation so route checks and policy-style lookups stay stable."
            permission-description-id="edit-permission-description"
            :save-description="form.isDirty ? 'Save the updated label and group metadata, then return to the permissions index.' : 'You can close this editor now, or keep reviewing the current catalog entry.'"
            save-heading="Finish this permission update"
            style="--motion-order: 1"
            :can-edit-key="false"
            :can-submit="canUpdate"
            :form="form"
            :groups="props.groups"
            :show-save="form.isDirty"
            :status="actionStatus"
            :status-tone="form.isDirty ? 'info' : 'muted'"
            submit-label="Save and Close"
            @close="closeToIndex"
            @delete="destroyPermission"
            @submit="updatePermission"
          />

          <Card class="surface-editor-rail motion-step gap-4 px-5 py-5" style="--motion-order: 2">
            <p class="section-kicker">What stays stable</p>
            <h2 class="text-lg font-semibold tracking-tight">The key is fixed. The catalog language is not.</h2>
            <p class="text-sm leading-6 text-muted-foreground">
              Use the editable fields to make the permission clearer for future role designers and auditors. If you move this permission into a different group or rewrite the group description, that shared metadata updates everywhere the group is
              shown.
            </p>
          </Card>
        </div>
      </div>
    </section>
  </div>
</template>
