<script setup lang="ts">
import { Link, setLayoutProps } from '@inertiajs/vue3';
import { computed } from 'vue';
import AdminIndexHeaderCell from '@/components/admin/AdminIndexHeaderCell.vue';
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
import { create, edit, index } from '@/routes/admin/roles';
import { adminPermissions } from '@/types/admin-permissions';
import type {
  AdminRolesIndexColumn,
  AdminRolesIndexPageProps,
} from '@/types/page-props';
defineOptions({
  layout: AppLayout,
});

setLayoutProps({
  breadcrumbs: [
    { title: 'Dashboard', href: dashboard.url() },
    { title: 'Roles', href: index.url() },
  ],
});

const props = defineProps<AdminRolesIndexPageProps>();

const { can } = useAbility();
const canCreate = computed(() => can(adminPermissions.rolesCreate));
const canUpdate = computed(() => can(adminPermissions.rolesUpdate));
const {
  clearFilters,
  setFilters,
  selectedFiltersFor,
  sortDirectionFor,
  toggleSort,
} = useAdminIndexTableQuery<AdminRolesIndexColumn>({
  getQuery: () => props.query,
  getUrl: (query) =>
    index.url({
      query: {
        ...query,
        page: undefined,
      },
    }),
  only: ['roles', 'filterOptions', 'query'],
});
</script>

<template>
  <div id="admin-roles-index-page" class="motion-stage space-y-6 px-4">
    <div
      id="admin-roles-index-page-header"
      class="motion-step flex flex-wrap items-center justify-between gap-3"
      style="--motion-order: 0"
    >
      <h1 class="text-2xl font-semibold">Roles</h1>

      <Button
        v-if="canCreate"
        id="admin-roles-index-create-button"
        appearance="outline"
        as-child
        class="motion-sheen"
      >
        <Link :href="create.url()">Create New Role</Link>
      </Button>
    </div>

    <Card
      id="admin-roles-index-mobile-controls"
      variant="default"
      class="motion-step gap-4 px-4 py-4 md:hidden"
      style="--motion-order: 1"
    >
      <div class="space-y-1.5">
        <p class="section-kicker">Refine roles</p>
        <p class="text-sm leading-6 text-muted-foreground">
          Keep sorting and filtering available on narrow screens without relying
          on horizontal table scanning.
        </p>
      </div>

      <div class="grid gap-3">
        <AdminIndexHeaderCell
          as="toolbar"
          label="Display Name"
          column="display_name"
          :filter-options="props.filterOptions.display_name"
          :format-option-label="toTitleCase"
          :selected-filters="selectedFiltersFor('display_name')"
          :sort-direction="sortDirectionFor('display_name')"
          @clear-filters="
            (column) => {
              clearFilters(column as AdminRolesIndexColumn);
            }
          "
          @apply-filters="
            (column, values) =>
              setFilters(column as AdminRolesIndexColumn, values)
          "
          @toggle-sort="
            (column) => {
              toggleSort(column as AdminRolesIndexColumn);
            }
          "
        />
        <AdminIndexHeaderCell
          as="toolbar"
          label="Slug"
          column="slug"
          :filter-options="props.filterOptions.slug"
          :selected-filters="selectedFiltersFor('slug')"
          :sort-direction="sortDirectionFor('slug')"
          @clear-filters="
            (column) => {
              clearFilters(column as AdminRolesIndexColumn);
            }
          "
          @apply-filters="
            (column, values) =>
              setFilters(column as AdminRolesIndexColumn, values)
          "
          @toggle-sort="
            (column) => {
              toggleSort(column as AdminRolesIndexColumn);
            }
          "
        />
        <AdminIndexHeaderCell
          as="toolbar"
          label="Users"
          column="users"
          :filter-options="props.filterOptions.users"
          :selected-filters="selectedFiltersFor('users')"
          :sort-direction="sortDirectionFor('users')"
          @clear-filters="
            (column) => {
              clearFilters(column as AdminRolesIndexColumn);
            }
          "
          @apply-filters="
            (column, values) =>
              setFilters(column as AdminRolesIndexColumn, values)
          "
          @toggle-sort="
            (column) => {
              toggleSort(column as AdminRolesIndexColumn);
            }
          "
        />
      </div>
    </Card>

    <div
      v-if="props.roles.length"
      id="admin-roles-index-mobile-list"
      class="motion-step grid gap-3 md:hidden"
      style="--motion-order: 2"
    >
      <Card
        v-for="role in props.roles"
        :key="role.id"
        variant="default"
        class="gap-4 px-5 py-5"
      >
        <div class="flex items-start justify-between gap-4">
          <div class="min-w-0 space-y-1">
            <p class="section-kicker">Role</p>
            <Link
              v-if="canUpdate"
              :href="edit.url(role.id)"
              class="block text-lg font-semibold tracking-tight text-primary underline decoration-primary/40 underline-offset-4 transition-colors hover:text-primary/80 hover:decoration-primary"
            >
              {{ toTitleCase(role.name) }}
            </Link>
            <p v-else class="text-lg font-semibold tracking-tight">
              {{ toTitleCase(role.name) }}
            </p>
            <p class="text-sm font-medium text-muted-foreground italic">
              {{ role.name }}
            </p>
          </div>

          <div
            class="rounded-full border border-primary/30 bg-primary/12 px-3 py-1 text-xs font-semibold text-primary"
          >
            {{ role.users_count }} user{{ role.users_count === 1 ? '' : 's' }}
          </div>
        </div>
      </Card>
    </div>

    <Card
      v-else
      id="admin-roles-index-mobile-empty-state"
      variant="default"
      class="motion-step px-5 py-5 text-sm text-muted-foreground md:hidden"
      style="--motion-order: 2"
    >
      No roles match the current filters.
    </Card>

    <Card
      id="admin-roles-index-table-card"
      variant="default"
      class="motion-step hidden overflow-hidden py-0 md:block"
      style="--motion-order: 2"
    >
      <Table id="admin-roles-index-table">
        <TableHeader>
          <TableRow>
            <AdminIndexHeaderCell
              label="Display Name"
              column="display_name"
              :filter-options="props.filterOptions.display_name"
              :format-option-label="toTitleCase"
              :selected-filters="selectedFiltersFor('display_name')"
              :sort-direction="sortDirectionFor('display_name')"
              @clear-filters="
                (column) => {
                  clearFilters(column as AdminRolesIndexColumn);
                }
              "
              @apply-filters="
                (column, values) =>
                  setFilters(column as AdminRolesIndexColumn, values)
              "
              @toggle-sort="
                (column) => {
                  toggleSort(column as AdminRolesIndexColumn);
                }
              "
            />
            <AdminIndexHeaderCell
              label="Slug"
              column="slug"
              :filter-options="props.filterOptions.slug"
              :selected-filters="selectedFiltersFor('slug')"
              :sort-direction="sortDirectionFor('slug')"
              @clear-filters="
                (column) => {
                  clearFilters(column as AdminRolesIndexColumn);
                }
              "
              @apply-filters="
                (column, values) =>
                  setFilters(column as AdminRolesIndexColumn, values)
              "
              @toggle-sort="
                (column) => {
                  toggleSort(column as AdminRolesIndexColumn);
                }
              "
            />
            <AdminIndexHeaderCell
              label="Users"
              column="users"
              :filter-options="props.filterOptions.users"
              :selected-filters="selectedFiltersFor('users')"
              :sort-direction="sortDirectionFor('users')"
              @clear-filters="
                (column) => {
                  clearFilters(column as AdminRolesIndexColumn);
                }
              "
              @apply-filters="
                (column, values) =>
                  setFilters(column as AdminRolesIndexColumn, values)
              "
              @toggle-sort="
                (column) => {
                  toggleSort(column as AdminRolesIndexColumn);
                }
              "
            />
          </TableRow>
        </TableHeader>
        <TableBody>
          <TableRow v-for="role in props.roles" :key="role.id">
            <TableCell class="font-medium">
              <Link
                v-if="canUpdate"
                :href="edit.url(role.id)"
                class="font-semibold text-primary underline decoration-primary/40 underline-offset-4 transition-colors hover:text-primary/80 hover:decoration-primary"
              >
                {{ toTitleCase(role.name) }}
              </Link>
              <span v-else>{{ toTitleCase(role.name) }}</span>
            </TableCell>
            <TableCell
              class="text-xs font-medium text-muted-foreground italic"
              >{{ role.name }}</TableCell
            >
            <TableCell class="text-muted-foreground">{{
              role.users_count
            }}</TableCell>
          </TableRow>
          <TableRow v-if="props.roles.length === 0">
            <TableCell colspan="3" class="text-center text-muted-foreground">
              No roles match the current filters.
            </TableCell>
          </TableRow>
        </TableBody>
      </Table>
    </Card>
  </div>
</template>
