<script setup lang="ts">
import { Link, setLayoutProps } from '@inertiajs/vue3';
import { computed } from 'vue';
import AdminIndexHeaderCell from '@/components/admin/AdminIndexHeaderCell.vue';
import Button from '@/components/ui/button/Button.vue';
import Card from '@/components/ui/card/Card.vue';
import Table from '@/components/ui/table/Table.vue';
import TableBody from '@/components/ui/table/TableBody.vue';
import TableCell from '@/components/ui/table/TableCell.vue';
import TableHeader from '@/components/ui/table/TableHeader.vue';
import TableRow from '@/components/ui/table/TableRow.vue';
import { useAdminIndexTableQuery } from '@/composables/useAdminIndexTableQuery';
import { useAbility } from '@/composables/useAbility';
import AppLayout from '@/layouts/AppLayout.vue';
import { toTitleCase } from '@/lib/utils';
import { dashboard } from '@/routes/admin';
import { create, edit, index } from '@/routes/admin/roles';
import { adminPermissions } from '@/types/admin-permissions';
import type {
  AdminRolesIndexColumn,
  AdminRolesIndexPageProps,
} from '@/types/page-props';
defineOptions({
  layout: AppLayout,
});

setLayoutProps({
  breadcrumbs: [
    { title: 'Dashboard', href: dashboard.url() },
    { title: 'Roles', href: index.url() },
  ],
});

const props = defineProps<AdminRolesIndexPageProps>();

const { can } = useAbility();
const canCreate = computed(() => can(adminPermissions.rolesCreate));
const canUpdate = computed(() => can(adminPermissions.rolesUpdate));
const {
  clearFilters,
  setFilters,
  selectedFiltersFor,
  sortDirectionFor,
  toggleSort,
} = useAdminIndexTableQuery<AdminRolesIndexColumn>({
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
</script>

<template>
  <div class="space-y-6 px-4">
    <div class="flex flex-wrap items-center justify-between gap-3">
      <h1 class="text-2xl font-semibold">Roles</h1>

      <Button v-if="canCreate" appearance="outline" as-child>
        <Link :href="create.url()">Create New Role</Link>
      </Button>
    </div>

    <Card variant="default" class="overflow-hidden py-0">
      <Table>
        <TableHeader>
          <TableRow>
            <AdminIndexHeaderCell
              label="Display Name"
              column="display_name"
              :filter-options="props.filterOptions.display_name"
              :format-option-label="toTitleCase"
              :selected-filters="selectedFiltersFor('display_name')"
              :sort-direction="sortDirectionFor('display_name')"
              @clear-filters="
                (column) => {
                  clearFilters(column as AdminRolesIndexColumn);
                }
              "
              @apply-filters="
                (column, values) =>
                  setFilters(column as AdminRolesIndexColumn, values)
              "
              @toggle-sort="
                (column) => {
                  toggleSort(column as AdminRolesIndexColumn);
                }
              "
            />
            <AdminIndexHeaderCell
              label="Slug"
              column="slug"
              :filter-options="props.filterOptions.slug"
              :selected-filters="selectedFiltersFor('slug')"
              :sort-direction="sortDirectionFor('slug')"
              @clear-filters="
                (column) => {
                  clearFilters(column as AdminRolesIndexColumn);
                }
              "
              @apply-filters="
                (column, values) =>
                  setFilters(column as AdminRolesIndexColumn, values)
              "
              @toggle-sort="
                (column) => {
                  toggleSort(column as AdminRolesIndexColumn);
                }
              "
            />
            <AdminIndexHeaderCell
              label="Users"
              column="users"
              :filter-options="props.filterOptions.users"
              :selected-filters="selectedFiltersFor('users')"
              :sort-direction="sortDirectionFor('users')"
              @clear-filters="
                (column) => {
                  clearFilters(column as AdminRolesIndexColumn);
                }
              "
              @apply-filters="
                (column, values) =>
                  setFilters(column as AdminRolesIndexColumn, values)
              "
              @toggle-sort="
                (column) => {
                  toggleSort(column as AdminRolesIndexColumn);
                }
              "
            />
          </TableRow>
        </TableHeader>
        <TableBody>
          <TableRow v-for="role in props.roles" :key="role.id">
            <TableCell class="font-medium">
              <Link
                v-if="canUpdate"
                :href="edit.url(role.id)"
                class="font-semibold text-primary underline decoration-primary/40 underline-offset-4 transition-colors hover:text-primary/80 hover:decoration-primary"
              >
                {{ toTitleCase(role.name) }}
              </Link>
              <span v-else>{{ toTitleCase(role.name) }}</span>
            </TableCell>
            <TableCell
              class="text-xs font-medium text-muted-foreground italic"
              >{{ role.name }}</TableCell
            >
            <TableCell class="text-muted-foreground">{{
              role.users_count
            }}</TableCell>
          </TableRow>
          <TableRow v-if="props.roles.length === 0">
            <TableCell colspan="3" class="text-center text-muted-foreground">
              No roles match the current filters.
            </TableCell>
          </TableRow>
        </TableBody>
      </Table>
    </Card>
  </div>
</template>
