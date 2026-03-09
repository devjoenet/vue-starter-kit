<script setup lang="ts">
import type { InertiaForm } from '@inertiajs/vue3';
import PermissionGroupSelect from '@/components/admin/PermissionGroupSelect.vue';
import Button from '@/components/ui/button/Button.vue';
import Card from '@/components/ui/card/Card.vue';
import Input from '@/components/ui/input/Input.vue';

type PermissionEditorFormData = {
  group: string;
  name: string;
};

defineProps<{
  canSubmit: boolean;
  form: InertiaForm<PermissionEditorFormData>;
  groups: string[];
  groupId: string;
  nameId: string;
  submitLabel: string;
}>();

defineEmits<{
  (event: 'submit'): void;
}>();
</script>

<template>
  <Card variant="default" class="px-6">
    <form class="space-y-4" @submit.prevent="$emit('submit')">
      <PermissionGroupSelect
        :id="groupId"
        v-model="form.group"
        :groups="groups"
        :disabled="!canSubmit"
        :error="form.errors.group"
      />

      <Input
        :id="nameId"
        v-model="form.name"
        name="name"
        label="Permission Name"
        variant="outlined"
        :disabled="!canSubmit"
        :state="form.errors.name ? 'error' : 'default'"
        :message="form.errors.name"
      />

      <div class="flex justify-end">
        <Button
          appearance="filled"
          type="submit"
          :disabled="!canSubmit || form.processing"
        >
          {{ submitLabel }}
        </Button>
      </div>
    </form>
  </Card>
</template>
