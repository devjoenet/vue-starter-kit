<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { useAttrs } from 'vue';
import AdminIndexHeaderCell from '@/components/admin/AdminIndexHeaderCell.vue';
import AdminIndexMobileControlsCard from '@/components/admin/AdminIndexMobileControlsCard.vue';
import AdminIndexTableCard from '@/components/admin/AdminIndexTableCard.vue';
import Card from '@/components/ui/card/Card.vue';
import Table from '@/components/ui/table/Table.vue';
import TableBody from '@/components/ui/table/TableBody.vue';
import TableCell from '@/components/ui/table/TableCell.vue';
import TableHeader from '@/components/ui/table/TableHeader.vue';
import TableRow from '@/components/ui/table/TableRow.vue';
import { toTitleCase } from '@/lib/utils';
import { edit } from '@/routes/admin/roles';
import type { AdminRolesIndexColumn, RoleListItem } from '@/types/admin/roles';

defineOptions({
  inheritAttrs: false,
});

type RoleIndexHeaderCell = {
  column: AdminRolesIndexColumn;
  filterOptions: string[];
  formatOptionLabel?: (value: string) => string;
  label: string;
  selectedFilters: string[];
  sortDirection: 'asc' | 'desc' | 'none';
};

const props = defineProps<{
  canUpdate: boolean;
  headerCells: RoleIndexHeaderCell[];
  roles: RoleListItem[];
}>();

const attrs = useAttrs();
</script>

<template>
  <section v-bind="attrs" data-slot="admin-roles-index-surface" class="space-y-3">
    <AdminIndexMobileControlsCard id="admin-roles-index-mobile-controls" kicker="Refine roles" description="Keep sorting and filtering available on narrow screens without relying on horizontal table scanning.">
      <AdminIndexHeaderCell v-for="headerCell in props.headerCells" :key="`toolbar-${headerCell.column}`" as="toolbar" v-bind="headerCell" />
    </AdminIndexMobileControlsCard>

    <div v-if="props.roles.length" id="admin-roles-index-mobile-list" class="grid gap-3 md:hidden">
      <Card v-for="role in props.roles" :key="role.id" appearance="filled" variant="neutral" class="gap-4 px-5 py-5">
        <div class="flex items-start justify-between gap-4">
          <div class="min-w-0 space-y-1">
            <p class="section-kicker">Role</p>
            <Link v-if="canUpdate" :href="edit.url(role.id)" class="block text-lg font-semibold tracking-tight text-primary underline decoration-primary/40 underline-offset-4 transition-colors hover:text-primary/80 hover:decoration-primary">
              {{ toTitleCase(role.name) }}
            </Link>
            <p v-else class="text-lg font-semibold tracking-tight">
              {{ toTitleCase(role.name) }}
            </p>
            <p class="text-sm font-medium text-muted-foreground italic">
              {{ role.name }}
            </p>
          </div>

          <div class="rounded-full border border-primary/30 bg-primary/12 px-3 py-1 text-xs font-semibold text-primary">{{ role.users_count }} user{{ role.users_count === 1 ? '' : 's' }}</div>
        </div>
      </Card>
    </div>

    <Card v-else id="admin-roles-index-mobile-empty-state" appearance="filled" variant="neutral" class="px-5 py-5 text-sm text-muted-foreground md:hidden"> No roles match the current filters. </Card>

    <AdminIndexTableCard id="admin-roles-index-table-card">
      <Table id="admin-roles-index-table">
        <TableHeader>
          <TableRow>
            <AdminIndexHeaderCell v-for="headerCell in props.headerCells" :key="headerCell.column" v-bind="headerCell" />
          </TableRow>
        </TableHeader>
        <TableBody>
          <TableRow v-for="role in props.roles" :key="role.id">
            <TableCell class="font-medium">
              <Link v-if="canUpdate" :href="edit.url(role.id)" class="font-semibold text-primary underline decoration-primary/40 underline-offset-4 transition-colors hover:text-primary/80 hover:decoration-primary">
                {{ toTitleCase(role.name) }}
              </Link>
              <span v-else>{{ toTitleCase(role.name) }}</span>
            </TableCell>
            <TableCell class="text-xs font-medium text-muted-foreground italic">{{ role.name }}</TableCell>
            <TableCell class="text-muted-foreground">{{ role.users_count }}</TableCell>
          </TableRow>
          <TableRow v-if="props.roles.length === 0">
            <TableCell colspan="3" class="text-center text-muted-foreground"> No roles match the current filters. </TableCell>
          </TableRow>
        </TableBody>
      </Table>
    </AdminIndexTableCard>
  </section>
</template>
