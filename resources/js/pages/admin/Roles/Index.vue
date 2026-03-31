<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import AdminIndexHeaderCell from '@/components/admin/AdminIndexHeaderCell.vue';
import AdminIndexMobileControlsCard from '@/components/admin/AdminIndexMobileControlsCard.vue';
import AdminIndexPageHeader from '@/components/admin/AdminIndexPageHeader.vue';
import AdminIndexTableCard from '@/components/admin/AdminIndexTableCard.vue';
import Button from '@/components/ui/button/Button.vue';
import Card from '@/components/ui/card/Card.vue';
import Table from '@/components/ui/table/Table.vue';
import TableBody from '@/components/ui/table/TableBody.vue';
import TableCell from '@/components/ui/table/TableCell.vue';
import TableHeader from '@/components/ui/table/TableHeader.vue';
import TableRow from '@/components/ui/table/TableRow.vue';
import { useAbility } from '@/composables/useAbility';
import { useAdminIndexTableQuery } from '@/composables/useAdminIndexTableQuery';
import { adminPageLayout, setAdminBreadcrumbs } from '@/lib/page-layouts';
import { toTitleCase } from '@/lib/utils';
import { create, edit, index } from '@/routes/admin/roles';
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
};

const headerCells = computed<RoleIndexHeaderCell[]>(() => [
  {
    label: 'Display Name',
    column: 'display_name',
    filterOptions: props.filterOptions.display_name,
    formatOptionLabel: toTitleCase,
  },
  {
    label: 'Slug',
    column: 'slug',
    filterOptions: props.filterOptions.slug,
  },
  {
    label: 'Users',
    column: 'users',
    filterOptions: props.filterOptions.users,
  },
]);

const getHeaderCellProps = (headerCell: RoleIndexHeaderCell) => ({
  ...headerCell,
  ...headerCellBindings(headerCell.column),
});
</script>

<template>
  <div id="admin-roles-index-page" class="motion-stage space-y-6 px-4">
    <AdminIndexPageHeader id="admin-roles-index-page-header" title="Roles" style="--motion-order: 0">
      <template #actions>
        <Button v-if="canCreate" id="admin-roles-index-create-button" appearance="outline" as-child class="motion-sheen">
          <Link :href="create.url()">Create New Role</Link>
        </Button>
      </template>
    </AdminIndexPageHeader>

    <AdminIndexMobileControlsCard id="admin-roles-index-mobile-controls" kicker="Refine roles" description="Keep sorting and filtering available on narrow screens without relying on horizontal table scanning." style="--motion-order: 1">
      <AdminIndexHeaderCell v-for="headerCell in headerCells" :key="`toolbar-${headerCell.column}`" as="toolbar" v-bind="getHeaderCellProps(headerCell)" />
    </AdminIndexMobileControlsCard>

    <div v-if="props.roles.length" id="admin-roles-index-mobile-list" class="motion-step grid gap-3 md:hidden" style="--motion-order: 2">
      <Card v-for="role in props.roles" :key="role.id" variant="default" class="gap-4 px-5 py-5">
        <div class="flex items-start justify-between gap-4">
          <div class="min-w-0 space-y-1">
            <p class="section-kicker">Role</p>
            <Link v-if="canUpdate" :href="edit.url(role.id)" class="block text-lg font-semibold tracking-tight text-primary underline decoration-primary/40 underline-offset-4 transition-colors hover:text-primary/80 hover:decoration-primary">
              {{ toTitleCase(role.name) }}
            </Link>
            <p v-else class="text-lg font-semibold tracking-tight">
              {{ toTitleCase(role.name) }}
            </p>
            <p class="text-sm font-medium text-muted-foreground italic">
              {{ role.name }}
            </p>
          </div>

          <div class="rounded-full border border-primary/30 bg-primary/12 px-3 py-1 text-xs font-semibold text-primary">{{ role.users_count }} user{{ role.users_count === 1 ? '' : 's' }}</div>
        </div>
      </Card>
    </div>

    <Card v-else id="admin-roles-index-mobile-empty-state" variant="default" class="motion-step px-5 py-5 text-sm text-muted-foreground md:hidden" style="--motion-order: 2"> No roles match the current filters. </Card>

    <AdminIndexTableCard id="admin-roles-index-table-card" style="--motion-order: 2">
      <Table id="admin-roles-index-table">
        <TableHeader>
          <TableRow>
            <AdminIndexHeaderCell v-for="headerCell in headerCells" :key="headerCell.column" v-bind="getHeaderCellProps(headerCell)" />
          </TableRow>
        </TableHeader>
        <TableBody>
          <TableRow v-for="role in props.roles" :key="role.id">
            <TableCell class="font-medium">
              <Link v-if="canUpdate" :href="edit.url(role.id)" class="font-semibold text-primary underline decoration-primary/40 underline-offset-4 transition-colors hover:text-primary/80 hover:decoration-primary">
                {{ toTitleCase(role.name) }}
              </Link>
              <span v-else>{{ toTitleCase(role.name) }}</span>
            </TableCell>
            <TableCell class="text-xs font-medium text-muted-foreground italic">{{ role.name }}</TableCell>
            <TableCell class="text-muted-foreground">{{ role.users_count }}</TableCell>
          </TableRow>
          <TableRow v-if="props.roles.length === 0">
            <TableCell colspan="3" class="text-center text-muted-foreground"> No roles match the current filters. </TableCell>
          </TableRow>
        </TableBody>
      </Table>
    </AdminIndexTableCard>
  </div>
</template>
