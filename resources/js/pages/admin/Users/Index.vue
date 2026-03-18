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
import { useAbility } from '@/composables/useAbility';
import { useAdminIndexTableQuery } from '@/composables/useAdminIndexTableQuery';
import AppLayout from '@/layouts/AppLayout.vue';
import { toTitleCase } from '@/lib/utils';
import { dashboard } from '@/routes/admin';
import { create, edit, index } from '@/routes/admin/users';
import { adminPermissions } from '@/types/admin-permissions';
import type { AdminUsersIndexColumn, AdminUsersIndexPageProps } from '@/types/page-props';

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
const { clearFilters, setFilters, selectedFiltersFor, sortDirectionFor, toggleSort } = useAdminIndexTableQuery<AdminUsersIndexColumn>({
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
  <div id="admin-users-index-page" class="motion-stage space-y-6 px-4">
    <div id="admin-users-index-page-header" class="motion-step flex flex-wrap items-center justify-between gap-3" style="--motion-order: 0">
      <h1 class="text-2xl font-semibold">Users</h1>

      <Button v-if="canCreate" id="admin-users-index-create-button" appearance="outline" as-child class="motion-sheen">
        <Link :href="create.url()">Create New User</Link>
      </Button>
    </div>

    <Card id="admin-users-index-mobile-controls" variant="default" class="motion-step gap-4 px-4 py-4 md:hidden" style="--motion-order: 1">
      <div class="space-y-1.5">
        <p class="section-kicker">Refine users</p>
        <p class="text-sm leading-6 text-muted-foreground">Filter or sort each column without relying on the desktop table header.</p>
      </div>

      <div class="grid gap-3">
        <AdminIndexHeaderCell
          as="toolbar"
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
          @apply-filters="(column, values) => setFilters(column as AdminUsersIndexColumn, values)"
          @toggle-sort="
            (column) => {
              toggleSort(column as AdminUsersIndexColumn);
            }
          "
        />
        <AdminIndexHeaderCell
          as="toolbar"
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
          @apply-filters="(column, values) => setFilters(column as AdminUsersIndexColumn, values)"
          @toggle-sort="
            (column) => {
              toggleSort(column as AdminUsersIndexColumn);
            }
          "
        />
        <AdminIndexHeaderCell
          as="toolbar"
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
          @apply-filters="(column, values) => setFilters(column as AdminUsersIndexColumn, values)"
          @toggle-sort="
            (column) => {
              toggleSort(column as AdminUsersIndexColumn);
            }
          "
        />
      </div>
    </Card>

    <div v-if="props.users.data.length" id="admin-users-index-mobile-list" class="motion-step grid gap-3 md:hidden" style="--motion-order: 2">
      <Card v-for="user in props.users.data" :key="user.id" variant="default" class="gap-4 px-5 py-5">
        <div class="flex items-start justify-between gap-4">
          <div class="min-w-0 space-y-1">
            <p class="section-kicker">User</p>
            <Link v-if="canUpdate" :href="edit.url(user.id)" class="block text-lg font-semibold tracking-tight text-primary underline decoration-primary/40 underline-offset-4 transition-colors hover:text-primary/80 hover:decoration-primary">
              {{ user.name }}
            </Link>
            <p v-else class="text-lg font-semibold tracking-tight">
              {{ user.name }}
            </p>
            <p class="text-sm break-all text-muted-foreground">
              {{ user.email }}
            </p>
          </div>

          <div class="rounded-full border border-secondary/30 bg-secondary/14 px-3 py-1 text-xs font-semibold text-secondary">{{ user.roles.length }} role{{ user.roles.length === 1 ? '' : 's' }}</div>
        </div>

        <div class="space-y-2 border-t border-border/60 pt-4">
          <p class="text-[11px] font-semibold tracking-[0.18em] text-muted-foreground uppercase">Assigned roles</p>
          <div class="flex flex-wrap gap-1.5">
            <Badge v-for="role in user.roles" :key="`${user.id}-${role}`" variant="secondary">
              {{ role }}
            </Badge>
            <span v-if="!user.roles?.length" class="text-sm text-muted-foreground"> No roles assigned </span>
          </div>
        </div>
      </Card>
    </div>

    <Card v-else id="admin-users-index-mobile-empty-state" variant="default" class="motion-step px-5 py-5 text-sm text-muted-foreground md:hidden" style="--motion-order: 2"> No users match the current filters. </Card>

    <Card id="admin-users-index-table-card" variant="default" class="motion-step hidden overflow-hidden py-0 md:block" style="--motion-order: 2">
      <Table id="admin-users-index-table">
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
              @apply-filters="(column, values) => setFilters(column as AdminUsersIndexColumn, values)"
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
              @apply-filters="(column, values) => setFilters(column as AdminUsersIndexColumn, values)"
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
              @apply-filters="(column, values) => setFilters(column as AdminUsersIndexColumn, values)"
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
              <Link v-if="canUpdate" :href="edit.url(user.id)" class="font-semibold text-primary underline decoration-primary/40 underline-offset-4 transition-colors hover:text-primary/80 hover:decoration-primary">
                {{ user.name }}
              </Link>
              <span v-else>{{ user.name }}</span>
            </TableCell>
            <TableCell class="text-muted-foreground">{{ user.email }}</TableCell>
            <TableCell>
              <div class="flex flex-wrap gap-1.5">
                <Badge v-for="role in user.roles" :key="`${user.id}-${role}`" variant="secondary">
                  {{ role }}
                </Badge>
                <span v-if="!user.roles?.length" class="text-xs text-muted-foreground">No roles</span>
              </div>
            </TableCell>
          </TableRow>
          <TableRow v-if="props.users.data.length === 0">
            <TableCell colspan="3" class="text-center text-muted-foreground"> No users match the current filters. </TableCell>
          </TableRow>
        </TableBody>
      </Table>
    </Card>
  </div>
</template>
