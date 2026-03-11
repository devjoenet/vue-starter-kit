<script setup lang="ts">
import { Link, setLayoutProps } from '@inertiajs/vue3';
import { computed } from 'vue';
import AdminIndexHeaderCell from '@/components/admin/AdminIndexHeaderCell.vue';
import Badge from '@/components/ui/badge/Badge.vue';
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
import { create, edit, index } from '@/routes/admin/users';
import { adminPermissions } from '@/types/admin-permissions';
import type {
  AdminUsersIndexColumn,
  AdminUsersIndexPageProps,
} from '@/types/page-props';
defineOptions({
  layout: AppLayout,
});

setLayoutProps({
  breadcrumbs: [
    { title: 'Dashboard', href: dashboard.url() },
    { title: 'Users', href: index.url() },
  ],
});

const props = defineProps<AdminUsersIndexPageProps>();

const { can } = useAbility();

const canCreate = computed(() => can(adminPermissions.usersCreate));
const canUpdate = computed(() => can(adminPermissions.usersUpdate));
const {
  clearFilters,
  setFilters,
  selectedFiltersFor,
  sortDirectionFor,
  toggleSort,
} = useAdminIndexTableQuery<AdminUsersIndexColumn>({
  getQuery: () => props.query,
  getUrl: (query) =>
    index.url({
      query: {
        ...query,
        page: undefined,
      },
    }),
  only: ['users', 'filterOptions', 'query'],
});
</script>

<template>
  <div class="space-y-6 px-4">
    <div class="flex flex-wrap items-center justify-between gap-3">
      <h1 class="text-2xl font-semibold">Users</h1>

      <Button v-if="canCreate" appearance="outline" as-child>
        <Link :href="create.url()">Create New User</Link>
      </Button>
    </div>

    <Card variant="default" class="overflow-hidden py-0">
      <Table>
        <TableHeader>
          <TableRow>
            <AdminIndexHeaderCell
              label="Name"
              column="name"
              :filter-options="props.filterOptions.name"
              :format-option-label="toTitleCase"
              :selected-filters="selectedFiltersFor('name')"
              :sort-direction="sortDirectionFor('name')"
              @clear-filters="
                (column) => {
                  clearFilters(column as AdminUsersIndexColumn);
                }
              "
              @apply-filters="
                (column, values) =>
                  setFilters(column as AdminUsersIndexColumn, values)
              "
              @toggle-sort="
                (column) => {
                  toggleSort(column as AdminUsersIndexColumn);
                }
              "
            />
            <AdminIndexHeaderCell
              label="Email"
              column="email"
              :filter-options="props.filterOptions.email"
              :selected-filters="selectedFiltersFor('email')"
              :sort-direction="sortDirectionFor('email')"
              @clear-filters="
                (column) => {
                  clearFilters(column as AdminUsersIndexColumn);
                }
              "
              @apply-filters="
                (column, values) =>
                  setFilters(column as AdminUsersIndexColumn, values)
              "
              @toggle-sort="
                (column) => {
                  toggleSort(column as AdminUsersIndexColumn);
                }
              "
            />
            <AdminIndexHeaderCell
              label="Roles"
              column="roles"
              :filter-options="props.filterOptions.roles"
              :format-option-label="toTitleCase"
              :selected-filters="selectedFiltersFor('roles')"
              :sort-direction="sortDirectionFor('roles')"
              @clear-filters="
                (column) => {
                  clearFilters(column as AdminUsersIndexColumn);
                }
              "
              @apply-filters="
                (column, values) =>
                  setFilters(column as AdminUsersIndexColumn, values)
              "
              @toggle-sort="
                (column) => {
                  toggleSort(column as AdminUsersIndexColumn);
                }
              "
            />
          </TableRow>
        </TableHeader>
        <TableBody>
          <TableRow v-for="user in props.users.data" :key="user.id">
            <TableCell class="font-medium">
              <Link
                v-if="canUpdate"
                :href="edit.url(user.id)"
                class="font-semibold text-primary underline decoration-primary/40 underline-offset-4 transition-colors hover:text-primary/80 hover:decoration-primary"
              >
                {{ user.name }}
              </Link>
              <span v-else>{{ user.name }}</span>
            </TableCell>
            <TableCell class="text-muted-foreground">{{
              user.email
            }}</TableCell>
            <TableCell>
              <div class="flex flex-wrap gap-1.5">
                <Badge
                  v-for="role in user.roles"
                  :key="`${user.id}-${role}`"
                  variant="secondary"
                >
                  {{ role }}
                </Badge>
                <span
                  v-if="!user.roles?.length"
                  class="text-xs text-muted-foreground"
                  >No roles</span
                >
              </div>
            </TableCell>
          </TableRow>
          <TableRow v-if="props.users.data.length === 0">
            <TableCell colspan="3" class="text-center text-muted-foreground">
              No users match the current filters.
            </TableCell>
          </TableRow>
        </TableBody>
      </Table>
    </Card>
  </div>
</template>
