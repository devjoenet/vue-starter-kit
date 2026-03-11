<script setup lang="ts">
import { computed, ref } from 'vue';
import AssignmentTableCard from '@/components/admin/AssignmentTableCard.vue';
import AdminIndexHeaderCell from '@/components/admin/AdminIndexHeaderCell.vue';
import Checkbox from '@/components/ui/checkbox/Checkbox.vue';
import Table from '@/components/ui/table/Table.vue';
import TableBody from '@/components/ui/table/TableBody.vue';
import TableCell from '@/components/ui/table/TableCell.vue';
import TableHead from '@/components/ui/table/TableHead.vue';
import TableHeader from '@/components/ui/table/TableHeader.vue';
import TableRow from '@/components/ui/table/TableRow.vue';
import { toTitleCase } from '@/lib/utils';
import type { RoleOption } from '@/types/page-props';

type RoleSortColumn = 'display_name' | 'slug';
type SortDirection = 'none' | 'asc' | 'desc';

const props = defineProps<{
  canAssign: boolean;
  error?: string;
  roles: RoleOption[];
  selectedRoleNames: string[];
}>();

defineEmits<{
  (
    event: 'toggle-role',
    roleName: string,
    value: boolean | 'indeterminate',
  ): void;
}>();

const activeSortColumn = ref<RoleSortColumn>('display_name');
const activeSortDirection = ref<SortDirection>('asc');
const selectedFilters = ref<Partial<Record<RoleSortColumn, string[]>>>({});

const filterOptions = computed(() => ({
  display_name: Array.from(
    new Set(props.roles.map((role) => role.name)),
  ).sort(),
  slug: Array.from(new Set(props.roles.map((role) => role.name))).sort(),
}));

const selectedFiltersFor = (column: RoleSortColumn): string[] =>
  selectedFilters.value[column] ?? [];

const setFilters = (column: RoleSortColumn, values: string[]) => {
  selectedFilters.value = {
    ...selectedFilters.value,
    [column]: [...new Set(values)].sort(),
  };

  if (selectedFilters.value[column]?.length === 0) {
    delete selectedFilters.value[column];
  }
};

const clearFilters = (column: RoleSortColumn) => {
  const nextFilters = { ...selectedFilters.value };

  delete nextFilters[column];

  selectedFilters.value = nextFilters;
};

const sortDirectionFor = (column: RoleSortColumn): SortDirection =>
  activeSortColumn.value === column ? activeSortDirection.value : 'none';

const toggleSort = (column: RoleSortColumn) => {
  if (activeSortColumn.value !== column) {
    activeSortColumn.value = column;
    activeSortDirection.value = 'asc';

    return;
  }

  if (activeSortDirection.value === 'none') {
    activeSortDirection.value = 'asc';

    return;
  }

  if (activeSortDirection.value === 'asc') {
    activeSortDirection.value = 'desc';

    return;
  }

  activeSortDirection.value = 'none';
};

const filteredRoles = computed(() =>
  props.roles.filter((role) => {
    const displayNameFilters = selectedFiltersFor('display_name');
    const slugFilters = selectedFiltersFor('slug');

    return (
      (!displayNameFilters.length || displayNameFilters.includes(role.name)) &&
      (!slugFilters.length || slugFilters.includes(role.name))
    );
  }),
);

const sortedRoles = computed(() =>
  [...filteredRoles.value].sort((left, right) => {
    if (activeSortDirection.value === 'none') {
      return left.id - right.id;
    }

    const direction = activeSortDirection.value === 'asc' ? 1 : -1;

    if (activeSortColumn.value === 'slug') {
      return left.name.localeCompare(right.name) * direction;
    }

    return (
      toTitleCase(left.name).localeCompare(toTitleCase(right.name)) * direction
    );
  }),
);
</script>

<template>
  <AssignmentTableCard
    :error="error"
    description="Filter and assign roles from one table."
    title="Roles"
  >
    <Table wrapper-class="rounded-none border-0">
      <TableHeader>
        <TableRow>
          <TableHead class="w-14 text-center">Access</TableHead>
          <AdminIndexHeaderCell
            label="Display Name"
            column="display_name"
            :filter-options="filterOptions.display_name"
            :format-option-label="toTitleCase"
            :selected-filters="selectedFiltersFor('display_name')"
            :sort-direction="sortDirectionFor('display_name')"
            @apply-filters="
              (column, values) => setFilters(column as RoleSortColumn, values)
            "
            @clear-filters="
              (column) => {
                clearFilters(column as RoleSortColumn);
              }
            "
            @toggle-sort="
              (column) => {
                toggleSort(column as RoleSortColumn);
              }
            "
          />
          <AdminIndexHeaderCell
            label="Slug"
            column="slug"
            head-class="hidden md:table-cell"
            :filter-options="filterOptions.slug"
            :selected-filters="selectedFiltersFor('slug')"
            :sort-direction="sortDirectionFor('slug')"
            @apply-filters="
              (column, values) => setFilters(column as RoleSortColumn, values)
            "
            @clear-filters="
              (column) => {
                clearFilters(column as RoleSortColumn);
              }
            "
            @toggle-sort="
              (column) => {
                toggleSort(column as RoleSortColumn);
              }
            "
          />
        </TableRow>
      </TableHeader>
      <TableBody>
        <TableRow v-for="role in sortedRoles" :key="role.id">
          <TableCell class="text-center">
            <Checkbox
              :disabled="!canAssign"
              :model-value="selectedRoleNames.includes(role.name)"
              @update:model-value="
                (value) => $emit('toggle-role', role.name, value)
              "
            />
          </TableCell>
          <TableCell class="font-medium">
            {{ toTitleCase(role.name) }}
          </TableCell>
          <TableCell
            class="hidden text-xs font-medium text-muted-foreground italic md:table-cell"
          >
            {{ role.name }}
          </TableCell>
        </TableRow>
        <TableRow v-if="sortedRoles.length === 0">
          <TableCell colspan="3" class="text-center text-muted-foreground">
            No roles match the current filters.
          </TableCell>
        </TableRow>
      </TableBody>
    </Table>
  </AssignmentTableCard>
</template>
