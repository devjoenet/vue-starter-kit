import { computed, ref } from 'vue';
import { toTitleCase } from '@/lib/utils';
import type { PermissionsByGroup } from '@/types/page-props';

type PermissionSortDirection = 'none' | 'asc' | 'desc';
type PermissionSortColumn = 'group' | 'name';

export type PermissionTableRow = {
  id: number;
  name: string;
  group: string;
  suffix: string;
};

export function usePermissionTable(
  permissionsByGroup: () => PermissionsByGroup,
) {
  const search = ref('');
  const groupFilter = ref('');
  const sortDirections = ref<
    Record<PermissionSortColumn, PermissionSortDirection>
  >({
    group: 'asc',
    name: 'asc',
  });

  const permissionRows = computed<PermissionTableRow[]>(() =>
    Object.entries(permissionsByGroup()).flatMap(([group, items]) =>
      items.map((permission) => ({
        ...permission,
        group,
        suffix: permission.name.startsWith(`${group}.`)
          ? permission.name.slice(group.length + 1)
          : permission.name,
      })),
    ),
  );

  const groupOptions = computed(() => {
    const groups = Array.from(
      new Set(permissionRows.value.map((permission) => permission.group)),
    ).sort((left, right) => left.localeCompare(right));

    return [
      { value: '', label: '' },
      ...groups.map((group) => ({
        value: group,
        label: toTitleCase(group),
      })),
    ];
  });

  const filteredRows = computed(() => {
    const searchTerm = search.value.trim().toLowerCase();

    return permissionRows.value.filter((permission) => {
      const matchesGroup =
        !groupFilter.value || permission.group === groupFilter.value;

      if (!matchesGroup) {
        return false;
      }

      if (!searchTerm) {
        return true;
      }

      return (
        permission.name.toLowerCase().includes(searchTerm) ||
        permission.suffix.toLowerCase().includes(searchTerm) ||
        permission.group.toLowerCase().includes(searchTerm)
      );
    });
  });

  const sortedRows = computed(() =>
    [...filteredRows.value].sort((left, right) => {
      if (sortDirections.value.group !== 'none') {
        const groupComparison = left.group.localeCompare(right.group);
        if (groupComparison !== 0) {
          return sortDirections.value.group === 'asc'
            ? groupComparison
            : groupComparison * -1;
        }
      }

      if (sortDirections.value.name !== 'none') {
        const nameComparison = left.suffix.localeCompare(right.suffix);
        if (nameComparison !== 0) {
          return sortDirections.value.name === 'asc'
            ? nameComparison
            : nameComparison * -1;
        }
      }

      return 0;
    }),
  );

  const toggleSort = (column: PermissionSortColumn) => {
    const current = sortDirections.value[column];

    if (current === 'none') {
      sortDirections.value[column] = 'asc';
      return;
    }

    if (current === 'asc') {
      sortDirections.value[column] = 'desc';
      return;
    }

    sortDirections.value[column] = 'none';
  };

  return {
    filteredRows,
    groupFilter,
    groupOptions,
    permissionRows,
    search,
    sortDirections,
    sortedRows,
    toggleSort,
  };
}
