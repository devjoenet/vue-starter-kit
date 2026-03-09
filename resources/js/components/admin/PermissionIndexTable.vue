<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
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
import { usePermissionTable } from '@/composables/usePermissionTable';
import { toTitleCase } from '@/lib/utils';
import { edit } from '@/routes/admin/permissions';
import type { PermissionsByGroup } from '@/types/page-props';
import { SquarePenIcon, TrashIcon } from 'lucide-vue-next';

const props = defineProps<{
  canDelete: boolean;
  canUpdate: boolean;
  permissionsByGroup: PermissionsByGroup;
}>();

defineEmits<{
  (event: 'delete-permission', permissionId: number): void;
}>();

const {
  groupFilter,
  groupOptions,
  search,
  sortDirections,
  sortedRows,
  toggleSort,
} = usePermissionTable(() => props.permissionsByGroup);
</script>

<template>
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
        <div class="flex h-fit justify-items-center self-center md:text-right">
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
          <TableCell class="text-muted-foreground">
            {{ toTitleCase(permission.group) }}
          </TableCell>
          <TableCell class="font-medium">
            {{ toTitleCase(permission.suffix) }}
          </TableCell>
          <TableCell class="text-muted-foreground">
            {{ permission.name }}
          </TableCell>
          <TableCell>
            <div class="flex items-center justify-end gap-2">
              <Button v-if="canUpdate" appearance="outline" size="sm" as-child>
                <Link :href="edit.url(permission.id)">
                  <SquarePenIcon />
                </Link>
              </Button>

              <Button
                v-if="canDelete"
                appearance="outline"
                variant="destructive"
                size="sm"
                @click="$emit('delete-permission', permission.id)"
              >
                <TrashIcon />
              </Button>
            </div>
          </TableCell>
        </TableRow>
        <TableRow v-if="!sortedRows.length">
          <TableCell colspan="4" class="text-center text-muted-foreground">
            No permissions match the current filters.
          </TableCell>
        </TableRow>
      </TableBody>
    </Table>
  </Card>
</template>
