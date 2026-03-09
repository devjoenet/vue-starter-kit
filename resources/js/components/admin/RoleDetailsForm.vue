<script setup lang="ts">
import type { InertiaForm } from '@inertiajs/vue3';
import Button from '@/components/ui/button/Button.vue';
import Card from '@/components/ui/card/Card.vue';
import Input from '@/components/ui/input/Input.vue';
import type { UpdateRoleRequest } from '@/types/wayfinder-generated';

defineProps<{
  canUpdate: boolean;
  form: InertiaForm<UpdateRoleRequest>;
}>();

defineEmits<{
  (event: 'submit'): void;
}>();
</script>

<template>
  <Card variant="default" class="px-6">
    <h2 class="text-lg font-semibold">Role Details</h2>

    <form class="mt-4 space-y-4" @submit.prevent="$emit('submit')">
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
      />

      <div class="flex justify-end">
        <Button
          appearance="filled"
          type="submit"
          :disabled="!canUpdate || form.processing"
        >
          Save Role Name
        </Button>
      </div>
    </form>
  </Card>
</template>
