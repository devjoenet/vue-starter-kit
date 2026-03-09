<script setup lang="ts">
import { Link, useForm } from '@inertiajs/vue3';
import { computed, h } from 'vue';
import Button from '@/components/ui/button/Button.vue';
import Card from '@/components/ui/card/Card.vue';
import Input from '@/components/ui/input/Input.vue';
import Select from '@/components/ui/select/Select.vue';
import Table from '@/components/ui/table/Table.vue';
import TableBody from '@/components/ui/table/TableBody.vue';
import TableCell from '@/components/ui/table/TableCell.vue';
import TableHead from '@/components/ui/table/TableHead.vue';
import TableHeader from '@/components/ui/table/TableHeader.vue';
import TableRow from '@/components/ui/table/TableRow.vue';
import { useAbility } from '@/composables/useAbility';
import { usePermissionTable } from '@/composables/usePermissionTable';
import AppLayout from '@/layouts/AppLayout.vue';
import { toTitleCase } from '@/lib/utils';
import { dashboard } from '@/routes/admin';
import { create, destroy, edit, index } from '@/routes/admin/permissions';
import { adminPermissions } from '@/types/admin-permissions';
import type { AdminPermissionsIndexPageProps } from '@/types/page-props';
import { TrashIcon, SquarePenIcon } from 'lucide-vue-next';
defineOptions({
  layout: (_: unknown, page: unknown) =>
    h(
      AppLayout,
      {
        breadcrumbs: [
          { title: 'Dashboard', href: dashboard.url() },
          { title: 'Permissions', href: index.url() },
        ],
      },
      () => page,
    ),
});

const props = defineProps<AdminPermissionsIndexPageProps>();

const { can } = useAbility();
const canCreate = computed(() => can(adminPermissions.permissionsCreate));
const canUpdate = computed(() => can(adminPermissions.permissionsUpdate));
const canDelete = computed(() => can(adminPermissions.permissionsDelete));

const del = useForm({});
const { groupFilter, groupOptions, search, sortDirections, sortedRows, toggleSort } =
  usePermissionTable(() => props.permissionsByGroup);

const destroyPermission = (id: number) => {
  if (!canDelete.value) return;
  if (!confirm('Delete this permission?')) return;
  del.delete(destroy.url(id), {
    only: ['permissionsByGroup', 'flash'],
    preserveScroll: true,
  });
};
</script>

<template>
  <div class="space-y-6 px-4">
    <div class="flex flex-wrap items-center justify-between gap-3">
      <h1 class="text-2xl font-semibold">Permissions</h1>

      <Button v-if="canCreate" appearance="outline" as-child>
        <Link :href="create.url()">Create New Permission</Link>
      </Button>
    </div>

    <Card variant="default" class="overflow-hidden py-0">
      <div class="border-b border-border/60 px-4 py-4">
        <div class="grid gap-3 md:grid-cols-[1fr_16rem_auto] md:items-end">
          <Input
            id="permissions-search"
            v-model="search"
            placeholder="Search permission or group..."
            variant="outlined"
          />
          <Select
            id="permissions-group-filter"
            v-model="groupFilter"
            :options="groupOptions"
            label="Group"
            placeholder=""
            variant="outlined"
          />
          <div
            class="flex h-fit justify-items-center self-center md:text-right"
          >
            <p class="text-lg leading-tight font-semibold">
              {{ sortedRows.length }} result{{
                sortedRows.length === 1 ? '' : 's'
              }}
            </p>
          </div>
        </div>
      </div>

      <Table>
        <TableHeader>
          <TableRow>
            <TableHead>
              <button
                type="button"
                class="inline-flex items-center gap-1"
                @click="toggleSort('group')"
              >
                Group
                <span class="text-[10px]">
                  {{
                    sortDirections.group === 'none'
                      ? '⇅'
                      : sortDirections.group === 'asc'
                        ? '▲'
                        : '▼'
                  }}
                </span>
              </button>
            </TableHead>
            <TableHead>
              <button
                type="button"
                class="inline-flex items-center gap-1"
                @click="toggleSort('name')"
              >
                Permission
                <span class="text-[10px]">
                  {{
                    sortDirections.name === 'none'
                      ? '⇅'
                      : sortDirections.name === 'asc'
                        ? '▲'
                        : '▼'
                  }}
                </span>
              </button>
            </TableHead>
            <TableHead>Permission to Check</TableHead>
            <TableHead class="w-[1%] text-left">Actions</TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          <TableRow v-for="permission in sortedRows" :key="permission.id">
            <TableCell class="text-muted-foreground">{{
              toTitleCase(permission.group)
            }}</TableCell>
            <TableCell class="font-medium">{{
              toTitleCase(permission.suffix)
            }}</TableCell>
            <TableCell class="text-muted-foreground">{{
              permission.name
            }}</TableCell>
            <TableCell>
              <div class="flex items-center justify-end gap-2">
                <Button
                  v-if="canUpdate"
                  appearance="outline"
                  size="sm"
                  as-child
                >
                  <Link :href="edit.url(permission.id)">
                    <SquarePenIcon />
                  </Link>
                </Button>

                <Button
                  v-if="canDelete"
                  appearance="outline"
                  variant="destructive"
                  size="sm"
                  @click="destroyPermission(permission.id)"
                >
                  <TrashIcon />
                </Button>
              </div>
            </TableCell>
          </TableRow>
          <TableRow v-if="!sortedRows.length">
            <TableCell colspan="4" class="text-center text-muted-foreground"
              >No permissions match the current filters.</TableCell
            >
          </TableRow>
        </TableBody>
      </Table>
    </Card>
  </div>
</template>
