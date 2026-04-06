<script setup lang="ts">
import type { InertiaForm } from '@inertiajs/vue3';
import Card from '@/components/ui/card/Card.vue';
import Input from '@/components/ui/input/Input.vue';
import { toTitleCase } from '@/lib/utils';

const props = withDefaults(
  defineProps<{
    description?: string;
    canUpdate: boolean;
    form: InertiaForm<{
      name: string;
    }>;
    nameId?: string;
    title?: string;
  }>(),
  {
    nameId: 'edit-role-name',
  },
);

const normalizeRoleNameForDisplay = () => {
  props.form.name = toTitleCase(props.form.name);
};
</script>

<template>
  <Card appearance="filled" variant="primary" class="rounded-[1.5rem] px-6 py-6">
    <div class="space-y-2">
      <p class="section-kicker">Role details</p>
      <h2 class="text-lg font-semibold tracking-tight">
        {{ title ?? 'Name the role for clear access reviews.' }}
      </h2>
      <p class="text-sm leading-6 text-muted-foreground">
        {{ description ?? 'Use a name that will still read clearly in assignments, filters, and policy conversations.' }}
      </p>
    </div>

    <div class="mt-6 space-y-4">
      <Input
        :id="nameId"
        :default-value="form.name"
        v-model="form.name"
        name="name"
        label="Role name"
        variant="outlined"
        :disabled="!canUpdate"
        :state="form.errors.name ? 'error' : 'default'"
        :message="form.errors.name"
        @blur="normalizeRoleNameForDisplay"
      />
    </div>
  </Card>
</template>
