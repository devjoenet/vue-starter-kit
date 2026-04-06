<script setup lang="ts">
import { computed } from 'vue';
import { cn } from 'tailwind-variants';
import AdminIndexHeaderCell from '@/components/admin/AdminIndexHeaderCell.vue';
import AssignmentTableCard from '@/components/admin/AssignmentTableCard.vue';
import { getCardInsetPanelClassNames } from '@/components/ui/card/variants';
import Checkbox from '@/components/ui/checkbox/Checkbox.vue';
import Table from '@/components/ui/table/Table.vue';
import TableBody from '@/components/ui/table/TableBody.vue';
import TableCell from '@/components/ui/table/TableCell.vue';
import TableHead from '@/components/ui/table/TableHead.vue';
import TableHeader from '@/components/ui/table/TableHeader.vue';
import TableRow from '@/components/ui/table/TableRow.vue';
import { usePermissionTable } from '@/composables/usePermissionTable';
import type { PermissionSortColumn } from '@/composables/usePermissionTable';
import { toTitleCase } from '@/lib/utils';
import type { PermissionsByGroup } from '@/types/admin/roles';

const props = defineProps<{
  canAssign: boolean;
  error?: string;
  permissionsByGroup: PermissionsByGroup;
  selectedPermissionNames: string[];
}>();

const emit = defineEmits<{
  (event: 'toggle-permission', permissionName: string, value: boolean | 'indeterminate'): void;
}>();

const { clearFilters, filterOptions, permissionRows, resultsLabel, selectedFiltersFor, setFilters, sortDirectionFor, sortedRows, toggleSort } = usePermissionTable(() => props.permissionsByGroup);
const groupLabelMap = computed(() => new Map(permissionRows.value.map((permission) => [permission.group, permission.groupLabel])));
const formatGroupFilterLabel = (value: string) => groupLabelMap.value.get(value) ?? toTitleCase(value);
</script>

<template>
  <AssignmentTableCard :error="error" description="Filter, sort, and assign permissions without leaving this role editor." :results-label="resultsLabel" title="Permission assignments">
    <div class="border-b border-border/60 px-6 py-5 md:hidden">
      <div class="space-y-1.5">
        <p class="section-kicker">Refine permissions</p>
        <p class="text-sm leading-6 text-muted-foreground">Narrow the permission list before you change what this role can do.</p>
      </div>

      <div class="mt-4 grid gap-3">
        <AdminIndexHeaderCell
          as="toolbar"
          label="Group"
          column="group"
          :filter-options="filterOptions.group"
          :format-option-label="formatGroupFilterLabel"
          :selected-filters="selectedFiltersFor('group')"
          :sort-direction="sortDirectionFor('group')"
          @apply-filters="(column, values) => setFilters(column as PermissionSortColumn, values)"
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
          as="toolbar"
          label="Permission"
          column="permission"
          :filter-options="filterOptions.permission"
          :format-option-label="(value) => value"
          :selected-filters="selectedFiltersFor('permission')"
          :sort-direction="sortDirectionFor('permission')"
          @apply-filters="(column, values) => setFilters(column as PermissionSortColumn, values)"
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
      </div>
    </div>

    <div v-if="sortedRows.length" class="grid gap-3 p-4 md:hidden">
      <label v-for="permission in sortedRows" :key="permission.id" :class="cn(getCardInsetPanelClassNames({ appearance: 'outline', variant: 'neutral' }), 'flex min-h-11 items-start gap-4 px-4 py-4', !canAssign ? 'opacity-70' : '')">
        <Checkbox class="mt-0.5 size-5" :disabled="!canAssign" :model-value="selectedPermissionNames.includes(permission.name)" @update:model-value="(value) => $emit('toggle-permission', permission.name, value)" />

        <span class="min-w-0 space-y-1">
          <span class="block text-sm font-semibold">
            {{ permission.label }}
          </span>
          <span v-if="permission.description" class="block text-sm leading-6 text-muted-foreground">
            {{ permission.description }}
          </span>
          <span class="block text-sm font-medium text-muted-foreground">
            {{ permission.groupLabel }}
          </span>
          <span class="block text-xs text-muted-foreground/90">
            {{ permission.name }}
          </span>
          <span v-if="permission.groupDescription" class="block text-xs leading-5 text-muted-foreground/80">
            {{ permission.groupDescription }}
          </span>
        </span>
      </label>
    </div>

    <div v-else class="p-4 md:hidden">
      <div :class="cn(getCardInsetPanelClassNames({ appearance: 'tinted', variant: 'accent' }), 'px-4 py-4')">
        <p class="text-sm font-semibold">No permissions match the current filters.</p>
        <p class="mt-1 text-sm leading-6 text-muted-foreground">Clear the filters or keep this role's current access footprint.</p>
      </div>
    </div>

    <Table wrapper-class="hidden rounded-none border-0 md:block">
      <TableHeader>
        <TableRow>
          <TableHead class="w-14 text-center">Access</TableHead>
          <AdminIndexHeaderCell
            label="Group"
            column="group"
            :filter-options="filterOptions.group"
            :format-option-label="formatGroupFilterLabel"
            :selected-filters="selectedFiltersFor('group')"
            :sort-direction="sortDirectionFor('group')"
            @apply-filters="(column, values) => setFilters(column as PermissionSortColumn, values)"
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
            :format-option-label="(value) => value"
            :selected-filters="selectedFiltersFor('permission')"
            :sort-direction="sortDirectionFor('permission')"
            @apply-filters="(column, values) => setFilters(column as PermissionSortColumn, values)"
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
            @apply-filters="(column, values) => setFilters(column as PermissionSortColumn, values)"
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
            <Checkbox class="size-5" :disabled="!canAssign" :model-value="selectedPermissionNames.includes(permission.name)" @update:model-value="(value) => $emit('toggle-permission', permission.name, value)" />
          </TableCell>
          <TableCell class="text-muted-foreground">
            <p class="font-medium text-foreground">
              {{ permission.groupLabel }}
            </p>
            <p v-if="permission.groupDescription" class="mt-1 text-sm leading-6 text-muted-foreground">
              {{ permission.groupDescription }}
            </p>
          </TableCell>
          <TableCell class="font-medium">
            <p>{{ permission.label }}</p>
            <p v-if="permission.description" class="mt-1 text-sm leading-6 text-muted-foreground">
              {{ permission.description }}
            </p>
          </TableCell>
          <TableCell class="hidden text-muted-foreground xl:table-cell">
            {{ permission.name }}
          </TableCell>
        </TableRow>
        <TableRow v-if="!sortedRows.length">
          <TableCell colspan="4" class="text-center text-muted-foreground"> No permissions match the current filters. </TableCell>
        </TableRow>
      </TableBody>
    </Table>

    <template #footer>
      <p class="text-xs leading-5 text-muted-foreground">Permission changes apply only when you save this editor.</p>
    </template>
  </AssignmentTableCard>
</template>
