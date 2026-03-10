<script setup lang="ts">
import type { InertiaForm } from '@inertiajs/vue3';
import UserIdentityFields from '@/components/UserIdentityFields.vue';
import Card from '@/components/ui/card/Card.vue';
import Input from '@/components/ui/input/Input.vue';
import type { UpdateUserRequest } from '@/types/wayfinder-generated';

defineProps<{
  canUpdate: boolean;
  form: InertiaForm<UpdateUserRequest>;
}>();
</script>

<template>
  <Card variant="default" class="px-6">
    <h2 class="text-lg font-semibold">Details</h2>

    <div class="mt-4 space-y-4">
      <UserIdentityFields
        name-id="edit-user-name"
        email-id="edit-user-email"
        v-model:name="form.name"
        v-model:email="form.email"
        :disabled="!canUpdate"
        :name-error="form.errors.name"
        :email-error="form.errors.email"
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
    </div>
  </Card>
</template>
