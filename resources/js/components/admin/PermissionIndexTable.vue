<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import AdminIndexHeaderCell from '@/components/admin/AdminIndexHeaderCell.vue';
import Card from '@/components/ui/card/Card.vue';
import Table from '@/components/ui/table/Table.vue';
import TableBody from '@/components/ui/table/TableBody.vue';
import TableCell from '@/components/ui/table/TableCell.vue';
import TableHeader from '@/components/ui/table/TableHeader.vue';
import TableRow from '@/components/ui/table/TableRow.vue';
import { useAdminIndexTableQuery } from '@/composables/useAdminIndexTableQuery';
import { toTitleCase } from '@/lib/utils';
import { edit, index } from '@/routes/admin/permissions';
import type {
  AdminPermissionsIndexColumn,
  AdminPermissionsIndexFilterOptions,
  AdminIndexQuery,
  PermissionIndexItem,
} from '@/types/page-props';

const props = defineProps<{
  canUpdate: boolean;
  filterOptions: AdminPermissionsIndexFilterOptions;
  permissions: PermissionIndexItem[];
  query: AdminIndexQuery<AdminPermissionsIndexColumn>;
}>();

const { clearFilters, selectedFiltersFor, sortDirectionFor, toggleFilter, toggleSort } =
  useAdminIndexTableQuery<AdminPermissionsIndexColumn>({
    getQuery: () => props.query,
    getUrl: (query) =>
      index.url({
        query: {
          ...query,
          page: undefined,
        },
      }),
    only: ['permissions', 'filterOptions', 'query'],
  });
</script>

<template>
  <Card variant="default" class="overflow-hidden py-0">
    <Table>
      <TableHeader>
        <TableRow>
          <AdminIndexHeaderCell
            label="Group"
            column="group"
            :filter-options="props.filterOptions.group"
            :format-option-label="toTitleCase"
            :selected-filters="selectedFiltersFor('group')"
            :sort-direction="sortDirectionFor('group')"
            @clear-filters="
              (column) => {
                clearFilters(column as AdminPermissionsIndexColumn);
              }
            "
            @toggle-filter="
              (column, value, checked) =>
                toggleFilter(
                  column as AdminPermissionsIndexColumn,
                  value,
                  checked,
                )
            "
            @toggle-sort="
              (column) => {
                toggleSort(column as AdminPermissionsIndexColumn);
              }
            "
          />
          <AdminIndexHeaderCell
            label="Permission"
            column="permission"
            :filter-options="props.filterOptions.permission"
            :format-option-label="toTitleCase"
            :selected-filters="selectedFiltersFor('permission')"
            :sort-direction="sortDirectionFor('permission')"
            @clear-filters="
              (column) => {
                clearFilters(column as AdminPermissionsIndexColumn);
              }
            "
            @toggle-filter="
              (column, value, checked) =>
                toggleFilter(
                  column as AdminPermissionsIndexColumn,
                  value,
                  checked,
                )
            "
            @toggle-sort="
              (column) => {
                toggleSort(column as AdminPermissionsIndexColumn);
              }
            "
          />
          <AdminIndexHeaderCell
            label="Permission to Check"
            column="permission_check"
            :filter-options="props.filterOptions.permission_check"
            :selected-filters="selectedFiltersFor('permission_check')"
            :sort-direction="sortDirectionFor('permission_check')"
            @clear-filters="
              (column) => {
                clearFilters(column as AdminPermissionsIndexColumn);
              }
            "
            @toggle-filter="
              (column, value, checked) =>
                toggleFilter(
                  column as AdminPermissionsIndexColumn,
                  value,
                  checked,
                )
            "
            @toggle-sort="
              (column) => {
                toggleSort(column as AdminPermissionsIndexColumn);
              }
            "
          />
        </TableRow>
      </TableHeader>
      <TableBody>
        <TableRow v-for="permission in props.permissions" :key="permission.id">
          <TableCell class="text-muted-foreground">
            {{ toTitleCase(permission.group) }}
          </TableCell>
          <TableCell class="font-medium">
            <Link
              v-if="canUpdate"
              :href="edit.url(permission.id)"
              class="transition-colors hover:text-foreground/70"
            >
              {{ toTitleCase(permission.suffix) }}
            </Link>
            <span v-else>{{ toTitleCase(permission.suffix) }}</span>
          </TableCell>
          <TableCell class="text-muted-foreground">
            {{ permission.name }}
          </TableCell>
        </TableRow>
        <TableRow v-if="!props.permissions.length">
          <TableCell colspan="3" class="text-center text-muted-foreground">
            No permissions match the current filters.
          </TableCell>
        </TableRow>
      </TableBody>
    </Table>
  </Card>
</template>
