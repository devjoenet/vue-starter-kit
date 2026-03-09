<script setup lang="ts">
import type { InertiaForm } from '@inertiajs/vue3';
import Button from '@/components/ui/button/Button.vue';
import Card from '@/components/ui/card/Card.vue';
import Input from '@/components/ui/input/Input.vue';
import type { UpdateUserRequest } from '@/types/wayfinder-generated';

defineProps<{
  canUpdate: boolean;
  form: InertiaForm<UpdateUserRequest>;
  userLabel: string;
}>();

defineEmits<{
  (event: 'submit'): void;
}>();
</script>

<template>
  <Card variant="default" class="px-6">
    <h2 class="text-lg font-semibold">Details</h2>

    <form class="mt-4 space-y-4" @submit.prevent="$emit('submit')">
      <Input
        id="edit-user-name"
        v-model="form.name"
        name="name"
        label="Name"
        variant="outlined"
        :disabled="!canUpdate"
        :state="form.errors.name ? 'error' : 'default'"
        :message="form.errors.name"
      />

      <Input
        id="edit-user-email"
        v-model="form.email"
        type="email"
        name="email"
        label="Email"
        variant="outlined"
        :disabled="!canUpdate"
        :state="form.errors.email ? 'error' : 'default'"
        :message="form.errors.email"
      />

      <Input
        id="edit-user-password"
        v-model="form.password"
        type="password"
        name="password"
        label="New password (optional)"
        variant="outlined"
        :disabled="!canUpdate"
        :state="form.errors.password ? 'error' : 'default'"
        :message="form.errors.password"
      />

      <Input
        id="edit-user-password-confirmation"
        v-model="form.password_confirmation"
        type="password"
        name="password_confirmation"
        label="Confirm new password"
        variant="outlined"
        :disabled="!canUpdate"
        :state="form.errors.password_confirmation ? 'error' : 'default'"
        :message="form.errors.password_confirmation"
      />

      <div class="flex justify-end">
        <Button
          appearance="filled"
          type="submit"
          :disabled="!canUpdate || form.processing"
        >
          Save {{ userLabel }}
        </Button>
      </div>
    </form>
  </Card>
</template>
