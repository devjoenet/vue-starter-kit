<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import { cn } from 'tailwind-variants';
import AdminEditorAsideCard from '@/components/admin/AdminEditorAsideCard.vue';
import AdminEditorShell from '@/components/admin/AdminEditorShell.vue';
import AdminPageIntro from '@/components/admin/AdminPageIntro.vue';
import AssignmentTableCard from '@/components/admin/AssignmentTableCard.vue';
import EditPageActionRow from '@/components/admin/EditPageActionRow.vue';
import RoleDetailsForm from '@/components/admin/RoleDetailsForm.vue';
import { getCardInsetPanelClassNames } from '@/components/ui/card/variants';
import Checkbox from '@/components/ui/checkbox/Checkbox.vue';
import { useAbility } from '@/composables/useAbility';
import { useSelectionList } from '@/composables/useSelectionList';
import { adminPageLayout, setAdminBreadcrumbs } from '@/lib/page-layouts';
import { toKebabCase } from '@/lib/utils';
import { create, index, store } from '@/routes/admin/roles';
import { adminPermissions } from '@/types/admin-permissions';
import type { AdminRolesCreatePageProps } from '@/types/admin/roles';
import type { StoreRoleRequest } from '@/types/wayfinder-generated';
defineOptions({
  layout: adminPageLayout,
});

setAdminBreadcrumbs({ title: 'Roles', href: index.url() }, { title: 'Create', href: create.url() });

const props = defineProps<AdminRolesCreatePageProps>();

const { can } = useAbility();
const canCreate = computed(() => can(adminPermissions.rolesCreate));

const form = useForm<StoreRoleRequest>({
  name: '',
  user_ids: [] as number[],
});
const { hasSelectedValue, selectedValues, toggleSelectedValue } = useSelectionList<number>(form.user_ids ?? []);

const submit = () => {
  if (!canCreate.value) return;

  form.user_ids = [...selectedValues.value];
  form
    .transform((data) => ({
      ...data,
      name: toKebabCase(data.name),
    }))
    .post(store.url());
};

const selectedUsersLabel = computed(() => {
  const selectedCount = selectedValues.value.length;

  return `${selectedCount} user${selectedCount === 1 ? '' : 's'} selected`;
});

const closeToIndex = () => {
  router.visit(index.url());
};
</script>

<template>
  <Head title="Create role" />

  <div id="admin-roles-create-page" class="motion-stage px-4">
    <AdminEditorShell>
      <AdminPageIntro
        id="admin-roles-create-page-header"
        class="motion-step"
        description="Create a reusable access bundle first, then optionally seed it with the people who should receive it immediately."
        kicker="Role editor"
        style="--motion-order: 0"
        title="Create a reusable access role"
      />

      <form id="admin-roles-create-form" class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_18rem] xl:items-start" @submit.prevent="submit">
        <div id="admin-roles-create-sections" class="contents">
          <RoleDetailsForm
            id="admin-roles-create-details-card"
            class="motion-step"
            description="Use a role name that will still make sense in filters, audits, and client walkthroughs."
            name-id="create-role-name"
            style="--motion-order: 1"
            :can-update="canCreate"
            :form="form"
            title="Role details"
          />

          <AssignmentTableCard
            id="admin-roles-create-users-card"
            class="motion-step xl:col-span-2"
            description="Start with the people who should immediately receive this role, or leave it empty and assign later."
            :error="form.errors.user_ids"
            :results-label="selectedUsersLabel"
            style="--motion-order: 2"
            title="Initial assignees"
          >
            <div v-if="props.users.length" class="grid gap-3 p-4 sm:p-5">
              <label v-for="user in props.users" :key="user.id" :class="cn(getCardInsetPanelClassNames({ appearance: 'outline', variant: 'neutral' }), 'flex min-h-11 items-start gap-4 px-4 py-4', !canCreate ? 'opacity-70' : '')">
                <Checkbox class="mt-0.5 size-5" :disabled="!canCreate" :model-value="hasSelectedValue(user.id)" @update:model-value="(value) => toggleSelectedValue(user.id, value)" />

                <span class="min-w-0 space-y-1">
                  <span class="block truncate text-sm font-semibold">
                    {{ user.name }}
                  </span>
                  <span class="block truncate text-sm text-muted-foreground">
                    {{ user.email }}
                  </span>
                </span>
              </label>
            </div>

            <div v-else class="p-4 sm:p-5">
              <div :class="cn(getCardInsetPanelClassNames({ appearance: 'tinted', variant: 'accent' }), 'px-4 py-4')">
                <p class="text-sm font-semibold">No users are available to assign yet.</p>
                <p class="mt-1 text-sm leading-6 text-muted-foreground">Create the role now and connect people from the users surface later.</p>
              </div>
            </div>

            <template #footer>
              <p class="text-xs leading-5 text-muted-foreground">Initial assignees are optional. A role can be created empty and connected to people later.</p>
            </template>
          </AssignmentTableCard>
        </div>

        <aside class="space-y-4 xl:col-start-2 xl:row-start-1 xl:self-start">
          <AdminEditorAsideCard class="motion-step" style="--motion-order: 3">
            <p class="section-kicker">Naming guidance</p>
            <h2 class="text-lg font-semibold tracking-tight">Make the role obvious in one glance.</h2>
            <p class="text-sm leading-6 text-muted-foreground">Prefer names a client or teammate would understand immediately, such as Support Lead or Project Manager, instead of internal shorthand.</p>
          </AdminEditorAsideCard>

          <EditPageActionRow
            id="admin-roles-create-actions"
            class="motion-step xl:w-full"
            close-label="Back to roles"
            description="Create the role when the name and initial assignments look right, or return to the roles index without saving."
            heading="Create this role"
            style="--motion-order: 4"
            :can-save="canCreate"
            :processing="form.processing"
            save-label="Create role"
            @close="closeToIndex"
            @save="submit"
          />
        </aside>
      </form>
    </AdminEditorShell>
  </div>
</template>
