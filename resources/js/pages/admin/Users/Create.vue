<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import AdminEditorAsideCard from '@/components/admin/AdminEditorAsideCard.vue';
import AdminEditorShell from '@/components/admin/AdminEditorShell.vue';
import AdminPageIntro from '@/components/admin/AdminPageIntro.vue';
import EditPageActionRow from '@/components/admin/EditPageActionRow.vue';
import UserDetailsForm from '@/components/admin/UserDetailsForm.vue';
import { useAbility } from '@/composables/useAbility';
import { adminPageLayout, setAdminBreadcrumbs } from '@/lib/page-layouts';
import { create, index, store } from '@/routes/admin/users';
import { adminPermissions } from '@/types/admin-permissions';
import type { AdminUsersCreatePageProps } from '@/types/admin/users';
import type { StoreUserRequest } from '@/types/wayfinder-generated';
defineOptions({
  layout: adminPageLayout,
});

setAdminBreadcrumbs({ title: 'Users', href: index.url() }, { title: 'Create', href: create.url() });

defineProps<AdminUsersCreatePageProps>();

const { can } = useAbility();
const canCreate = computed(() => can(adminPermissions.usersCreate));

const form = useForm<StoreUserRequest>({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
});

const submit = () => {
  if (!canCreate.value) return;
  form.post(store.url());
};

const closeToIndex = () => {
  router.visit(index.url());
};
</script>

<template>
  <Head title="Create user" />

  <div id="admin-users-create-page" class="motion-stage px-4">
    <AdminEditorShell>
      <AdminPageIntro
        id="admin-users-create-page-header"
        class="motion-step"
        description="Create the account details first, then issue a password that can be rotated once the user takes ownership."
        kicker="User editor"
        style="--motion-order: 0"
        title="Create a workspace account"
      />

      <form id="admin-users-create-form" class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_18rem] xl:items-start" @submit.prevent="submit">
        <UserDetailsForm
          id="admin-users-create-form-card"
          class="motion-step"
          description="Capture the identity and sign-in details this account will need on day one."
          email-id="create-user-email"
          name-id="create-user-name"
          password-confirmation-label="Confirm password"
          password-confirmation-id="create-user-password-confirmation"
          password-label="Password"
          password-id="create-user-password"
          style="--motion-order: 1"
          :can-update="canCreate"
          :form="form"
          title="Account details"
        />

        <aside class="space-y-4 xl:self-start">
          <AdminEditorAsideCard class="motion-step" style="--motion-order: 2">
            <p class="section-kicker">Before you create</p>
            <h2 class="text-lg font-semibold tracking-tight">Start with the details that will stay stable.</h2>
            <p class="text-sm leading-6 text-muted-foreground">Use the real account name and primary email now. Passwords can change later, but identity details are what teammates and clients will scan first.</p>
          </AdminEditorAsideCard>

          <EditPageActionRow
            id="admin-users-create-actions"
            class="motion-step"
            close-label="Back to users"
            description="Create the account when the details look correct, or return to the users index without saving."
            heading="Create this account"
            save-id="admin-users-create-submit-button"
            style="--motion-order: 3"
            :can-save="canCreate"
            :processing="form.processing"
            save-label="Create user"
            @close="closeToIndex"
            @save="submit"
          />
        </aside>
      </form>
    </AdminEditorShell>
  </div>
</template>
