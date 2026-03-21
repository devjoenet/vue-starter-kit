<script setup lang="ts">
import type { InertiaForm } from '@inertiajs/vue3';
import EditPageActionRow from '@/components/admin/EditPageActionRow.vue';
import PermissionGroupSelect from '@/components/admin/PermissionGroupSelect.vue';
import Card from '@/components/ui/card/Card.vue';
import Input from '@/components/ui/input/Input.vue';
import type { PermissionGroupOption } from '@/types/page-props';

type PermissionEditorFormData = {
  group: string;
  group_label: string;
  group_description?: string;
  name: string;
  label: string;
  description?: string;
};

defineProps<{
  canEditKey?: boolean;
  canDelete?: boolean;
  canClose?: boolean;
  canSubmit: boolean;
  closeLabel?: string;
  deleteLabel?: string;
  description?: string;
  form: InertiaForm<PermissionEditorFormData>;
  groups: PermissionGroupOption[];
  groupId: string;
  heading?: string;
  groupDescriptionId?: string;
  groupLabelId?: string;
  labelId?: string;
  nameId: string;
  nameReadonly?: boolean;
  nameSupportingText?: string;
  permissionDescriptionId?: string;
  saveDescription?: string;
  saveHeading?: string;
  showSave?: boolean;
  submitLabel: string;
  status?: string;
  statusTone?: 'destructive' | 'info' | 'muted' | 'success' | 'warning';
}>();

defineEmits<{
  (event: 'close'): void;
  (event: 'delete'): void;
  (event: 'submit'): void;
}>();
</script>

<template>
  <Card variant="default" class="surface-editor-panel px-6">
    <form class="space-y-6" @submit.prevent="$emit('submit')">
      <div class="space-y-2">
        <p class="section-kicker">Permission details</p>
        <h2 class="text-lg font-semibold tracking-tight">
          {{ heading ?? 'Keep checks predictable and group-prefixed.' }}
        </h2>
        <p class="text-sm leading-6 text-muted-foreground">
          {{ description ?? 'Permissions should read clearly in policy checks, tables, and assignment flows.' }}
        </p>
      </div>

      <PermissionGroupSelect :id="groupId" v-model="form.group" :groups="groups" :disabled="!canSubmit" :error="form.errors.group" />

      <div class="grid gap-4 lg:grid-cols-[minmax(0,0.95fr)_minmax(0,1.05fr)]">
        <Input
          :id="groupLabelId ?? 'permission-group-label'"
          v-model="form.group_label"
          name="group_label"
          label="Group Label"
          variant="outlined"
          :disabled="!canSubmit"
          :state="form.errors.group_label ? 'error' : 'default'"
          :message="form.errors.group_label"
          supporting-text="Shared group metadata is reused by every permission assigned to this group."
        />

        <Input
          :id="groupDescriptionId ?? 'permission-group-description'"
          v-model="form.group_description"
          name="group_description"
          label="Group Description"
          variant="outlined"
          textarea
          :rows="3"
          :disabled="!canSubmit"
          :state="form.errors.group_description ? 'error' : 'default'"
          :message="form.errors.group_description"
          supporting-text="Use this to explain the business area or audience the group governs."
        />
      </div>

      <div class="surface-editor-action-zone rounded-[1.25rem] px-4 py-4">
        <p class="text-sm font-semibold tracking-tight">Group metadata is shared.</p>
        <p class="mt-1 text-sm leading-6 text-muted-foreground">Updating the group label or description changes how every permission in this group is described across the catalog and assignment flows.</p>
      </div>

      <Input
        :id="nameId"
        v-model="form.name"
        name="name"
        label="Permission Key"
        variant="outlined"
        :disabled="!canSubmit"
        :readonly="nameReadonly || !canEditKey"
        :state="form.errors.name ? 'error' : 'default'"
        :message="form.errors.name"
        :supporting-text="nameSupportingText ?? 'This machine key powers route checks and policy-style permission lookups.'"
      />

      <Input
        :id="labelId ?? 'permission-label'"
        v-model="form.label"
        name="label"
        label="Permission Label"
        variant="outlined"
        :disabled="!canSubmit"
        :state="form.errors.label ? 'error' : 'default'"
        :message="form.errors.label"
        supporting-text="This human-facing label appears in the admin catalog and role assignment UI."
      />

      <Input
        :id="permissionDescriptionId ?? 'permission-description'"
        v-model="form.description"
        name="description"
        label="Permission Description"
        variant="outlined"
        textarea
        :rows="3"
        :disabled="!canSubmit"
        :state="form.errors.description ? 'error' : 'default'"
        :message="form.errors.description"
        supporting-text="Use the description to spell out what access this permission actually grants."
      />

      <EditPageActionRow
        :can-delete="canDelete"
        :can-save="showSave ?? true"
        :close-label="closeLabel"
        :delete-label="deleteLabel"
        :description="saveDescription ?? 'When the permission name looks right, save and return to the permissions index.'"
        :heading="saveHeading ?? 'Finish this permission change'"
        :processing="form.processing"
        :save-label="submitLabel"
        :status="status"
        :status-tone="statusTone"
        @close="$emit('close')"
        @delete="$emit('delete')"
        @save="$emit('submit')"
      />
    </form>
  </Card>
</template>
