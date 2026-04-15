<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import AdminIndexPageHeader from '@/components/admin/AdminIndexPageHeader.vue';
import RoleIndexSurface from '@/components/admin/RoleIndexSurface.vue';
import Button from '@/components/ui/button/Button.vue';
import { useAbility } from '@/composables/useAbility';
import { useAdminIndexTableQuery } from '@/composables/useAdminIndexTableQuery';
import { adminPageLayout, setAdminBreadcrumbs } from '@/lib/page-layouts';
import { toTitleCase } from '@/lib/utils';
import { create, index } from '@/routes/admin/roles';
import { adminPermissions } from '@/types/admin-permissions';
import type { AdminRolesIndexColumn, AdminRolesIndexPageProps } from '@/types/admin/roles';
defineOptions({
  layout: adminPageLayout,
});

setAdminBreadcrumbs({ title: 'Roles', href: index.url() });

const props = defineProps<AdminRolesIndexPageProps>();

const { can } = useAbility();
const canCreate = computed(() => can(adminPermissions.rolesCreate));
const canUpdate = computed(() => can(adminPermissions.rolesUpdate));
const { headerCellBindings } = useAdminIndexTableQuery<AdminRolesIndexColumn>({
  getQuery: () => props.query,
  getUrl: (query) =>
    index.url({
      query: {
        ...query,
        page: undefined,
      },
    }),
  only: ['roles', 'filterOptions', 'query'],
});

type RoleIndexHeaderCell = {
  column: AdminRolesIndexColumn;
  filterOptions: string[];
  formatOptionLabel?: (value: string) => string;
  label: string;
  selectedFilters: string[];
  sortDirection: 'asc' | 'desc' | 'none';
};

const headerCells = computed<RoleIndexHeaderCell[]>(() => [
  {
    label: 'Display Name',
    column: 'display_name',
    filterOptions: props.filterOptions.display_name,
    formatOptionLabel: toTitleCase,
    ...headerCellBindings('display_name'),
  },
  {
    label: 'Slug',
    column: 'slug',
    filterOptions: props.filterOptions.slug,
    ...headerCellBindings('slug'),
  },
  {
    label: 'Users',
    column: 'users',
    filterOptions: props.filterOptions.users,
    ...headerCellBindings('users'),
  },
]);
</script>

<template>
  <Head title="Roles" />

  <div id="admin-roles-index-page" class="motion-stage space-y-6 px-4">
    <AdminIndexPageHeader id="admin-roles-index-page-header" title="Roles" style="--motion-order: 0">
      <template #actions>
        <Button v-if="canCreate" id="admin-roles-index-create-button" appearance="outline" as-child class="motion-sheen">
          <Link :href="create.url()">Create New Role</Link>
        </Button>
      </template>
    </AdminIndexPageHeader>

    <RoleIndexSurface class="motion-step" style="--motion-order: 1" :can-update="canUpdate" :header-cells="headerCells" :roles="props.roles" />
  </div>
</template>
