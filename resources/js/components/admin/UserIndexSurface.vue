<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { useAttrs } from 'vue';
import AdminIndexHeaderCell from '@/components/admin/AdminIndexHeaderCell.vue';
import AdminIndexMobileControlsCard from '@/components/admin/AdminIndexMobileControlsCard.vue';
import AdminIndexTableCard from '@/components/admin/AdminIndexTableCard.vue';
import Badge from '@/components/ui/badge/Badge.vue';
import Card from '@/components/ui/card/Card.vue';
import Table from '@/components/ui/table/Table.vue';
import TableBody from '@/components/ui/table/TableBody.vue';
import TableCell from '@/components/ui/table/TableCell.vue';
import TableHeader from '@/components/ui/table/TableHeader.vue';
import TableRow from '@/components/ui/table/TableRow.vue';
import { edit } from '@/routes/admin/users';
import type { PaginatedCollection } from '@/types/admin/shared';
import type { AdminUsersIndexColumn, UserListItem } from '@/types/admin/users';

defineOptions({
  inheritAttrs: false,
});

type UserIndexHeaderCell = {
  column: AdminUsersIndexColumn;
  filterOptions: string[];
  formatOptionLabel?: (value: string) => string;
  label: string;
  selectedFilters: string[];
  sortDirection: 'asc' | 'desc' | 'none';
};

const props = defineProps<{
  canUpdate: boolean;
  headerCells: UserIndexHeaderCell[];
  users: PaginatedCollection<UserListItem>;
}>();

const attrs = useAttrs();
</script>

<template>
  <section v-bind="attrs" data-slot="admin-users-index-surface" class="space-y-3">
    <AdminIndexMobileControlsCard id="admin-users-index-mobile-controls" kicker="Refine users" description="Filter or sort each column without relying on the desktop table header.">
      <AdminIndexHeaderCell v-for="headerCell in props.headerCells" :key="`toolbar-${headerCell.column}`" as="toolbar" v-bind="headerCell" />
    </AdminIndexMobileControlsCard>

    <div v-if="props.users.data.length" id="admin-users-index-mobile-list" class="grid gap-3 md:hidden">
      <Card v-for="user in props.users.data" :key="user.id" appearance="filled" variant="neutral" class="gap-4 px-5 py-5">
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

    <Card v-else id="admin-users-index-mobile-empty-state" appearance="filled" variant="neutral" class="px-5 py-5 text-sm text-muted-foreground md:hidden"> No users match the current filters. </Card>

    <AdminIndexTableCard id="admin-users-index-table-card">
      <Table id="admin-users-index-table">
        <TableHeader>
          <TableRow>
            <AdminIndexHeaderCell v-for="headerCell in props.headerCells" :key="headerCell.column" v-bind="headerCell" />
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
    </AdminIndexTableCard>
  </section>
</template>
