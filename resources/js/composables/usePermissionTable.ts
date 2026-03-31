import { computed, ref } from 'vue';
import type { PermissionsByGroup } from '@/types/admin/roles';

type PermissionSortDirection = 'none' | 'asc' | 'desc';
export type PermissionSortColumn = 'group' | 'permission' | 'permission_check';

export type PermissionTableRow = {
  description?: string | null;
  group: string;
  groupDescription?: string | null;
  groupLabel: string;
  id: number;
  label: string;
  name: string;
};

const nextSortDirection = (current: PermissionSortDirection): PermissionSortDirection => {
  if (current === 'none') {
    return 'asc';
  }

  if (current === 'asc') {
    return 'desc';
  }

  return 'none';
};

export function usePermissionTable(permissionsByGroup: () => PermissionsByGroup) {
  const activeSortColumn = ref<PermissionSortColumn>('group');
  const activeSortDirection = ref<PermissionSortDirection>('asc');
  const selectedFilters = ref<Partial<Record<PermissionSortColumn, string[]>>>({});

  const permissionRows = computed<PermissionTableRow[]>(() =>
    Object.entries(permissionsByGroup()).flatMap(([group, items]) =>
      items.map((permission) => ({
        id: permission.id,
        name: permission.name,
        label: permission.label,
        description: permission.description,
        group: permission.group || group,
        groupLabel: permission.group_label,
        groupDescription: permission.group_description,
      })),
    ),
  );

  const filterOptions = computed(() => ({
    group: Array.from(new Set(permissionRows.value.map((permission) => permission.group))).sort(),
    permission: Array.from(new Set(permissionRows.value.map((permission) => permission.label))).sort(),
    permission_check: Array.from(new Set(permissionRows.value.map((permission) => permission.name))).sort(),
  }));

  const matchesSelectedFilters = (column: PermissionSortColumn, value: string) => {
    const columnFilters = selectedFilters.value[column];

    return !columnFilters?.length || columnFilters.includes(value);
  };

  const filteredRows = computed(() =>
    permissionRows.value.filter((permission) => matchesSelectedFilters('group', permission.group) && matchesSelectedFilters('permission', permission.label) && matchesSelectedFilters('permission_check', permission.name)),
  );

  const sortedRows = computed(() =>
    [...filteredRows.value].sort((left, right) => {
      if (activeSortDirection.value === 'none') {
        return left.id - right.id;
      }

      const direction = activeSortDirection.value === 'asc' ? 1 : -1;

      if (activeSortColumn.value === 'group') {
        return left.groupLabel.localeCompare(right.groupLabel) * direction;
      }

      if (activeSortColumn.value === 'permission') {
        return left.label.localeCompare(right.label) * direction;
      }

      return left.name.localeCompare(right.name) * direction;
    }),
  );

  const resultsLabel = computed(() => {
    const count = sortedRows.value.length;

    return `${count} result${count === 1 ? '' : 's'}`;
  });

  const selectedFiltersFor = (column: PermissionSortColumn): string[] => selectedFilters.value[column] ?? [];

  const setFilters = (column: PermissionSortColumn, values: string[]) => {
    selectedFilters.value = {
      ...selectedFilters.value,
      [column]: [...new Set(values)].sort(),
    };

    if (selectedFilters.value[column]?.length === 0) {
      delete selectedFilters.value[column];
    }
  };

  const clearFilters = (column: PermissionSortColumn) => {
    const nextFilters = { ...selectedFilters.value };

    delete nextFilters[column];

    selectedFilters.value = nextFilters;
  };

  const sortDirectionFor = (column: PermissionSortColumn): PermissionSortDirection => (activeSortColumn.value === column ? activeSortDirection.value : 'none');

  const toggleSort = (column: PermissionSortColumn) => {
    if (activeSortColumn.value !== column) {
      activeSortColumn.value = column;
      activeSortDirection.value = 'asc';

      return;
    }

    activeSortDirection.value = nextSortDirection(activeSortDirection.value);
  };

  return {
    clearFilters,
    filterOptions,
    permissionRows,
    resultsLabel,
    selectedFiltersFor,
    setFilters,
    sortDirectionFor,
    sortedRows,
    toggleSort,
  };
}
