<script setup lang="ts">
import AssignmentTableCard from '@/components/admin/AssignmentTableCard.vue';
import AdminIndexHeaderCell from '@/components/admin/AdminIndexHeaderCell.vue';
import Checkbox from '@/components/ui/checkbox/Checkbox.vue';
import Table from '@/components/ui/table/Table.vue';
import TableBody from '@/components/ui/table/TableBody.vue';
import TableCell from '@/components/ui/table/TableCell.vue';
import TableHead from '@/components/ui/table/TableHead.vue';
import TableHeader from '@/components/ui/table/TableHeader.vue';
import TableRow from '@/components/ui/table/TableRow.vue';
import { usePermissionTable } from '@/composables/usePermissionTable';
import { toTitleCase } from '@/lib/utils';
import type { PermissionsByGroup } from '@/types/page-props';
import type { PermissionSortColumn } from '@/composables/usePermissionTable';

const props = defineProps<{
  canAssign: boolean;
  error?: string;
  permissionsByGroup: PermissionsByGroup;
  selectedPermissionNames: string[];
}>();

const emit = defineEmits<{
  (
    event: 'toggle-permission',
    permissionName: string,
    value: boolean | 'indeterminate',
  ): void;
}>();

const {
  clearFilters,
  filterOptions,
  resultsLabel,
  selectedFiltersFor,
  setFilters,
  sortDirectionFor,
  sortedRows,
  toggleSort,
} = usePermissionTable(() => props.permissionsByGroup);
</script>

<template>
  <AssignmentTableCard
    :error="error"
    description="Filter, sort, and assign permissions from one table."
    title="Permissions"
  >
    <Table wrapper-class="rounded-none border-0">
      <TableHeader>
        <TableRow>
          <TableHead class="w-14 text-center">Access</TableHead>
          <AdminIndexHeaderCell
            label="Group"
            column="group"
            :filter-options="filterOptions.group"
            :format-option-label="toTitleCase"
            :selected-filters="selectedFiltersFor('group')"
            :sort-direction="sortDirectionFor('group')"
            @apply-filters="
              (column, values) =>
                setFilters(column as PermissionSortColumn, values)
            "
            @clear-filters="
              (column) => {
                clearFilters(column as PermissionSortColumn);
              }
            "
            @toggle-sort="
              (column) => {
                toggleSort(column as PermissionSortColumn);
              }
            "
          />
          <AdminIndexHeaderCell
            label="Permission"
            column="permission"
            :filter-options="filterOptions.permission"
            :format-option-label="toTitleCase"
            :selected-filters="selectedFiltersFor('permission')"
            :sort-direction="sortDirectionFor('permission')"
            @apply-filters="
              (column, values) =>
                setFilters(column as PermissionSortColumn, values)
            "
            @clear-filters="
              (column) => {
                clearFilters(column as PermissionSortColumn);
              }
            "
            @toggle-sort="
              (column) => {
                toggleSort(column as PermissionSortColumn);
              }
            "
          />
          <AdminIndexHeaderCell
            label="Permission Check"
            column="permission_check"
            head-class="hidden xl:table-cell"
            :filter-options="filterOptions.permission_check"
            :selected-filters="selectedFiltersFor('permission_check')"
            :sort-direction="sortDirectionFor('permission_check')"
            @apply-filters="
              (column, values) =>
                setFilters(column as PermissionSortColumn, values)
            "
            @clear-filters="
              (column) => {
                clearFilters(column as PermissionSortColumn);
              }
            "
            @toggle-sort="
              (column) => {
                toggleSort(column as PermissionSortColumn);
              }
            "
          />
        </TableRow>
      </TableHeader>
      <TableBody>
        <TableRow v-for="permission in sortedRows" :key="permission.id">
          <TableCell class="text-center">
            <Checkbox
              :disabled="!canAssign"
              :model-value="selectedPermissionNames.includes(permission.name)"
              @update:model-value="
                (value) => $emit('toggle-permission', permission.name, value)
              "
            />
          </TableCell>
          <TableCell class="text-muted-foreground">
            {{ toTitleCase(permission.group) }}
          </TableCell>
          <TableCell class="font-medium">
            {{ toTitleCase(permission.suffix) }}
          </TableCell>
          <TableCell class="hidden text-muted-foreground xl:table-cell">
            {{ permission.name }}
          </TableCell>
        </TableRow>
        <TableRow v-if="!sortedRows.length">
          <TableCell colspan="4" class="text-center text-muted-foreground">
            No permissions match the current filters.
          </TableCell>
        </TableRow>
      </TableBody>
    </Table>
    <p
      class="border-t border-border/60 px-6 py-3 text-right text-xs font-semibold tracking-[0.16em] text-muted-foreground uppercase"
    >
      {{ resultsLabel }}
    </p>
  </AssignmentTableCard>
</template>
