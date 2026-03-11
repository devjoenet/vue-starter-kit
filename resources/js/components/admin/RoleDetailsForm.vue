<script setup lang="ts">
import type { InertiaForm } from '@inertiajs/vue3';
import Card from '@/components/ui/card/Card.vue';
import Input from '@/components/ui/input/Input.vue';
import { toTitleCase } from '@/lib/utils';
import type { UpdateRoleRequest } from '@/types/wayfinder-generated';

const props = defineProps<{
  canUpdate: boolean;
  form: InertiaForm<UpdateRoleRequest>;
}>();

const normalizeRoleNameForDisplay = () => {
  props.form.name = toTitleCase(props.form.name);
};
</script>

<template>
  <Card variant="default" class="px-6">
    <h2 class="text-lg font-semibold">Role Details</h2>

    <div class="mt-4 space-y-4">
      <Input
        id="edit-role-name"
        :default-value="form.name"
        v-model="form.name"
        name="name"
        label="Role Name"
        variant="outlined"
        :disabled="!canUpdate"
        :state="form.errors.name ? 'error' : 'default'"
        :message="form.errors.name"
        @blur="normalizeRoleNameForDisplay"
      />
    </div>
  </Card>
</template>
