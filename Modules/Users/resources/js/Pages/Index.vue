<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import AdminIndexPageHeader from '@/components/admin/AdminIndexPageHeader.vue';
import UserIndexSurface from '@/components/admin/UserIndexSurface.vue';
import Button from '@/components/ui/button/Button.vue';
import { useAbility } from '@/composables/useAbility';
import { useAdminIndexTableQuery } from '@/composables/useAdminIndexTableQuery';
import { adminPageLayout, setAdminBreadcrumbs } from '@/lib/page-layouts';
import { toTitleCase } from '@/lib/utils';
import { create, index } from '@/routes/admin/users';
import { adminPermissions } from '@/types/admin-permissions';
import type { AdminUsersIndexColumn, AdminUsersIndexPageProps } from '@/types/admin/users';

defineOptions({
  layout: adminPageLayout,
});

setAdminBreadcrumbs({ title: 'Users', href: index.url() });

const props = defineProps<AdminUsersIndexPageProps>();

const { can } = useAbility();

const canCreate = computed(() => can(adminPermissions.usersCreate));
const canUpdate = computed(() => can(adminPermissions.usersUpdate));
const { headerCellBindings } = useAdminIndexTableQuery<AdminUsersIndexColumn>({
  getQuery: () => props.query,
  getUrl: (query) =>
    index.url({
      query: {
        ...query,
        page: undefined,
      },
    }),
  only: ['users', 'filterOptions', 'query'],
});

type UserIndexHeaderCell = {
  column: AdminUsersIndexColumn;
  filterOptions: string[];
  formatOptionLabel?: (value: string) => string;
  label: string;
  selectedFilters: string[];
  sortDirection: 'asc' | 'desc' | 'none';
};

const headerCells = computed<UserIndexHeaderCell[]>(() => [
  {
    label: 'Name',
    column: 'name',
    filterOptions: props.filterOptions.name,
    formatOptionLabel: toTitleCase,
    ...headerCellBindings('name'),
  },
  {
    label: 'Email',
    column: 'email',
    filterOptions: props.filterOptions.email,
    ...headerCellBindings('email'),
  },
  {
    label: 'Roles',
    column: 'roles',
    filterOptions: props.filterOptions.roles,
    formatOptionLabel: toTitleCase,
    ...headerCellBindings('roles'),
  },
]);
</script>

<template>
  <Head title="Users" />

  <div id="admin-users-index-page" class="motion-stage space-y-6 px-4">
    <AdminIndexPageHeader id="admin-users-index-page-header" title="Users" style="--motion-order: 0">
      <template #actions>
        <Button v-if="canCreate" id="admin-users-index-create-button" appearance="outline" as-child class="motion-sheen">
          <Link :href="create.url()">Create New User</Link>
        </Button>
      </template>
    </AdminIndexPageHeader>

    <UserIndexSurface class="motion-step" style="--motion-order: 1" :can-update="canUpdate" :header-cells="headerCells" :users="props.users" />
  </div>
</template>
