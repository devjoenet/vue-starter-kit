<script setup lang="ts">
import type { InertiaForm } from '@inertiajs/vue3';
import UserIdentityFields from '@/components/UserIdentityFields.vue';
import Input from '@/components/ui/input/Input.vue';

withDefaults(
  defineProps<{
    canUpdate: boolean;
    description?: string;
    emailId?: string;
    form: InertiaForm<{
      email: string;
      name: string;
      password?: string;
      password_confirmation?: string;
    }>;
    nameId?: string;
    passwordConfirmationId?: string;
    passwordConfirmationLabel?: string;
    passwordId?: string;
    passwordLabel?: string;
    title?: string;
  }>(),
  {
    emailId: 'edit-user-email',
    nameId: 'edit-user-name',
    passwordConfirmationId: 'edit-user-password-confirmation',
    passwordId: 'edit-user-password',
  },
);
</script>

<template>
  <div class="surface-editor-panel rounded-[1.5rem] px-6 py-6">
    <div class="space-y-2">
      <p class="section-kicker">Account details</p>
      <h2 class="text-lg font-semibold tracking-tight">
        {{ title ?? 'Update the identity tied to this account.' }}
      </h2>
      <p class="text-sm leading-6 text-muted-foreground">
        {{ description ?? 'Keep the account name, email, and password state ready for handoff and access reviews.' }}
      </p>
    </div>

    <div class="mt-6 space-y-4">
      <UserIdentityFields :name-id="nameId" :email-id="emailId" v-model:name="form.name" v-model:email="form.email" :disabled="!canUpdate" :name-error="form.errors.name" :email-error="form.errors.email" />

      <Input
        :id="passwordId"
        v-model="form.password"
        type="password"
        name="password"
        :label="passwordLabel ?? 'New password (optional)'"
        variant="outlined"
        :disabled="!canUpdate"
        :state="form.errors.password ? 'error' : 'default'"
        :message="form.errors.password"
      />

      <Input
        :id="passwordConfirmationId"
        v-model="form.password_confirmation"
        type="password"
        name="password_confirmation"
        :label="passwordConfirmationLabel ?? 'Confirm new password'"
        variant="outlined"
        :disabled="!canUpdate"
        :state="form.errors.password_confirmation ? 'error' : 'default'"
        :message="form.errors.password_confirmation"
      />
    </div>
  </div>
</template>
