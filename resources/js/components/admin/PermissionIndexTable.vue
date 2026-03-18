<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import AdminIndexHeaderCell from '@/components/admin/AdminIndexHeaderCell.vue';
import Badge from '@/components/ui/badge/Badge.vue';
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
  PermissionGroupOption,
  PermissionIndexItem,
} from '@/types/page-props';
import { computed } from 'vue';

const props = defineProps<{
  canUpdate: boolean;
  filterOptions: AdminPermissionsIndexFilterOptions;
  groups: PermissionGroupOption[];
  permissions: PermissionIndexItem[];
  query: AdminIndexQuery<AdminPermissionsIndexColumn>;
}>();

const groupLabelMap = computed(
  () => new Map(props.groups.map((group) => [group.slug, group.label])),
);
const formatGroupOptionLabel = (value: string) =>
  groupLabelMap.value.get(value) ?? toTitleCase(value);
const formatIdentityOptionLabel = (value: string) => value;

const {
  clearFilters,
  setFilters,
  selectedFiltersFor,
  sortDirectionFor,
  toggleSort,
} = useAdminIndexTableQuery<AdminPermissionsIndexColumn>({
  getQuery: () => props.query,
  getUrl: (query) =>
    index.url({
      query: {
        ...query,
        page: undefined,
      },
    }),
  only: ['permissions', 'groups', 'filterOptions', 'query'],
});
</script>

<template>
  <Card variant="default" class="gap-4 px-4 py-4 md:hidden">
    <div class="space-y-1.5">
      <p class="section-kicker">Refine permissions</p>
      <p class="text-sm leading-6 text-muted-foreground">
        Keep the permission checks readable while preserving the same filter and
        sort controls used on desktop.
      </p>
    </div>

    <div class="grid gap-3">
      <AdminIndexHeaderCell
        as="toolbar"
        label="Permission"
        column="permission"
        :filter-options="props.filterOptions.permission"
        :format-option-label="formatIdentityOptionLabel"
        :selected-filters="selectedFiltersFor('permission')"
        :sort-direction="sortDirectionFor('permission')"
        @clear-filters="
          (column) => {
            clearFilters(column as AdminPermissionsIndexColumn);
          }
        "
        @apply-filters="
          (column, values) =>
            setFilters(column as AdminPermissionsIndexColumn, values)
        "
        @toggle-sort="
          (column) => {
            toggleSort(column as AdminPermissionsIndexColumn);
          }
        "
      />
      <AdminIndexHeaderCell
        as="toolbar"
        label="Group"
        column="group"
        :filter-options="props.filterOptions.group"
        :format-option-label="formatGroupOptionLabel"
        :selected-filters="selectedFiltersFor('group')"
        :sort-direction="sortDirectionFor('group')"
        @clear-filters="
          (column) => {
            clearFilters(column as AdminPermissionsIndexColumn);
          }
        "
        @apply-filters="
          (column, values) =>
            setFilters(column as AdminPermissionsIndexColumn, values)
        "
        @toggle-sort="
          (column) => {
            toggleSort(column as AdminPermissionsIndexColumn);
          }
        "
      />
      <AdminIndexHeaderCell
        as="toolbar"
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
        @apply-filters="
          (column, values) =>
            setFilters(column as AdminPermissionsIndexColumn, values)
        "
        @toggle-sort="
          (column) => {
            toggleSort(column as AdminPermissionsIndexColumn);
          }
        "
      />
    </div>
  </Card>

  <div v-if="props.permissions.length" class="grid gap-3 md:hidden">
    <Card
      v-for="permission in props.permissions"
      :key="permission.id"
      variant="default"
      class="gap-4 px-5 py-5"
    >
      <div class="flex items-start justify-between gap-4">
        <div class="min-w-0 space-y-1">
          <p class="section-kicker">Permission</p>
          <Link
            v-if="canUpdate"
            :href="edit.url(permission.id)"
            class="block text-lg font-semibold tracking-tight text-primary underline decoration-primary/40 underline-offset-4 transition-colors hover:text-primary/80 hover:decoration-primary"
          >
            {{ permission.label }}
          </Link>
          <p v-else class="text-lg font-semibold tracking-tight">
            {{ permission.label }}
          </p>
          <p
            v-if="permission.description"
            class="text-sm leading-6 text-muted-foreground"
          >
            {{ permission.description }}
          </p>
          <p class="text-sm font-medium wrap-break-word text-muted-foreground">
            {{ permission.name }}
          </p>
        </div>

        <Badge variant="info">{{ permission.group_label }}</Badge>
      </div>

      <p
        v-if="permission.group_description"
        class="text-sm leading-6 text-muted-foreground"
      >
        {{ permission.group_description }}
      </p>
    </Card>
  </div>

  <Card
    v-else
    variant="default"
    class="px-5 py-5 text-sm text-muted-foreground md:hidden"
  >
    No permissions match the current filters.
  </Card>

  <Card variant="default" class="hidden overflow-hidden py-0 md:block">
    <Table>
      <TableHeader>
        <TableRow>
          <AdminIndexHeaderCell
            label="Permission"
            column="permission"
            :filter-options="props.filterOptions.permission"
            :format-option-label="formatIdentityOptionLabel"
            :selected-filters="selectedFiltersFor('permission')"
            :sort-direction="sortDirectionFor('permission')"
            @clear-filters="
              (column) => {
                clearFilters(column as AdminPermissionsIndexColumn);
              }
            "
            @apply-filters="
              (column, values) =>
                setFilters(column as AdminPermissionsIndexColumn, values)
            "
            @toggle-sort="
              (column) => {
                toggleSort(column as AdminPermissionsIndexColumn);
              }
            "
          />
          <AdminIndexHeaderCell
            label="Group"
            column="group"
            :filter-options="props.filterOptions.group"
            :format-option-label="formatGroupOptionLabel"
            :selected-filters="selectedFiltersFor('group')"
            :sort-direction="sortDirectionFor('group')"
            @clear-filters="
              (column) => {
                clearFilters(column as AdminPermissionsIndexColumn);
              }
            "
            @apply-filters="
              (column, values) =>
                setFilters(column as AdminPermissionsIndexColumn, values)
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
            @apply-filters="
              (column, values) =>
                setFilters(column as AdminPermissionsIndexColumn, values)
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
          <TableCell class="font-medium">
            <Link
              v-if="canUpdate"
              :href="edit.url(permission.id)"
              class="font-semibold text-primary underline decoration-primary/40 underline-offset-4 transition-colors hover:text-primary/80 hover:decoration-primary"
            >
              {{ permission.label }}
            </Link>
            <span v-else>{{ permission.label }}</span>
            <p
              v-if="permission.description"
              class="mt-1 text-sm leading-6 text-muted-foreground"
            >
              {{ permission.description }}
            </p>
          </TableCell>
          <TableCell class="text-muted-foreground">
            <p class="font-medium text-foreground">
              {{ permission.group_label }}
            </p>
            <p
              v-if="permission.group_description"
              class="mt-1 text-sm leading-6 text-muted-foreground"
            >
              {{ permission.group_description }}
            </p>
          </TableCell>
          <TableCell class="font-mono text-xs text-muted-foreground">
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
