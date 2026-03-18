import { router } from '@inertiajs/vue3';
import { computed } from 'vue';
import type { AdminIndexQuery } from '@/types/page-props';

type UseAdminIndexTableQueryOptions<TColumn extends string> = {
  getQuery: () => AdminIndexQuery<TColumn>;
  getUrl: (query: AdminIndexVisitQuery<TColumn>) => string;
  only: string[];
};

type AdminIndexVisitQuery<TColumn extends string> = {
  direction?: AdminIndexQuery<TColumn>['direction'];
  filters: AdminIndexQuery<TColumn>['filters'];
  sort?: AdminIndexQuery<TColumn>['sort'];
};

const normalizeFilters = <TColumn extends string>(filters: Partial<Record<TColumn, string[]>>): Partial<Record<TColumn, string[]>> => {
  return Object.fromEntries(
    Object.entries(filters)
      .filter(([, values]) => Array.isArray(values) && values.length > 0)
      .map(([key, values]) => {
        const normalizedValues = Array.isArray(values) ? [...new Set<string>(values)].sort() : [];

        return [key, normalizedValues];
      }),
  ) as Partial<Record<TColumn, string[]>>;
};

export function useAdminIndexTableQuery<TColumn extends string>({ getQuery, getUrl, only }: UseAdminIndexTableQueryOptions<TColumn>) {
  const currentQuery = computed(() => getQuery());

  const visit = (nextQuery: AdminIndexVisitQuery<TColumn>) => {
    router.visit(getUrl(nextQuery), {
      preserveScroll: true,
      preserveState: true,
      replace: true,
      only,
    });
  };

  const sortDirectionFor = (column: TColumn): 'asc' | 'desc' | 'none' => {
    if (currentQuery.value.sort !== column) {
      return 'none';
    }

    return currentQuery.value.direction;
  };

  const selectedFiltersFor = (column: TColumn): string[] => {
    return currentQuery.value.filters[column] ?? [];
  };

  const toggleSort = (column: TColumn) => {
    if (currentQuery.value.sort !== column) {
      visit({
        sort: column,
        direction: 'asc',
        filters: normalizeFilters(currentQuery.value.filters),
      });

      return;
    }

    if (currentQuery.value.direction === 'asc') {
      visit({
        sort: column,
        direction: 'desc',
        filters: normalizeFilters(currentQuery.value.filters),
      });

      return;
    }

    visit({
      sort: undefined,
      direction: undefined,
      filters: normalizeFilters(currentQuery.value.filters),
    });
  };

  const toggleFilter = (column: TColumn, value: string, checked?: boolean | 'indeterminate') => {
    const currentValues = selectedFiltersFor(column);
    const shouldInclude = checked === undefined ? !currentValues.some((currentValue) => currentValue === value) : checked === true;

    const nextValues = shouldInclude ? [...currentValues, value] : currentValues.filter((currentValue) => currentValue !== value);

    visit({
      sort: currentQuery.value.sort,
      direction: currentQuery.value.direction,
      filters: normalizeFilters({
        ...currentQuery.value.filters,
        [column]: nextValues,
      }),
    });
  };

  const setFilters = (column: TColumn, values: string[]) => {
    visit({
      sort: currentQuery.value.sort,
      direction: currentQuery.value.direction,
      filters: normalizeFilters({
        ...currentQuery.value.filters,
        [column]: values,
      }),
    });
  };

  const clearFilters = (column: TColumn) => {
    const filters = { ...currentQuery.value.filters };

    delete filters[column];

    visit({
      sort: currentQuery.value.sort,
      direction: currentQuery.value.direction,
      filters: normalizeFilters(filters),
    });
  };

  return {
    clearFilters,
    selectedFiltersFor,
    setFilters,
    sortDirectionFor,
    toggleFilter,
    toggleSort,
  };
}
