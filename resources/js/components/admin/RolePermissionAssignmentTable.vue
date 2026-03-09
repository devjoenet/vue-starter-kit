<script setup lang="ts">
import { computed } from 'vue';
import Button from '@/components/ui/button/Button.vue';
import Card from '@/components/ui/card/Card.vue';
import Checkbox from '@/components/ui/checkbox/Checkbox.vue';
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
import type { PermissionsByGroup } from '@/types/page-props';

const props = defineProps<{
  canAssign: boolean;
  error?: string;
  permissionsByGroup: PermissionsByGroup;
  processing: boolean;
  selectedPermissionNames: string[];
}>();

const emit = defineEmits<{
  (
    event: 'toggle-permission',
    permissionName: string,
    value: boolean | 'indeterminate',
  ): void;
  (event: 'save'): void;
}>();

const { groupFilter, groupOptions, search, sortDirections, sortedRows, toggleSort } =
  usePermissionTable(() => props.permissionsByGroup);

const resultsLabel = computed(() => {
  const count = sortedRows.value.length;

  return `${count} result${count === 1 ? '' : 's'}`;
});
</script>

<template>
  <Card variant="default" class="overflow-hidden py-0">
    <div class="flex items-center justify-between gap-3 border-b border-border/60 px-6 py-4">
      <div class="space-y-1">
        <h2 class="text-lg font-semibold">Permissions</h2>
        <p class="text-sm text-muted-foreground">
          Filter, sort, and assign permissions from one table.
        </p>
      </div>

      <Button
        appearance="filled"
        :disabled="!canAssign || processing"
        @click="$emit('save')"
      >
        Save Permissions
      </Button>
    </div>

    <Table wrapper-class="rounded-none border-0">
      <TableHeader>
        <TableRow>
          <TableHead class="w-14 text-center">Access</TableHead>
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
          <TableHead class="hidden xl:table-cell">Permission Check</TableHead>
        </TableRow>
        <TableRow>
          <TableHead />
          <TableHead class="align-top">
            <Select
              id="role-permissions-group-filter"
              v-model="groupFilter"
              :options="groupOptions"
              placeholder="All groups"
              variant="outlined"
              trigger-class="min-w-[11rem]"
            />
          </TableHead>
          <TableHead class="align-top">
            <Input
              id="role-permissions-search"
              v-model="search"
              placeholder="Filter permissions..."
              variant="outlined"
            />
          </TableHead>
          <TableHead class="hidden xl:table-cell text-right text-xs font-semibold text-muted-foreground uppercase tracking-[0.16em]">
            {{ resultsLabel }}
          </TableHead>
        </TableRow>
      </TableHeader>
      <TableBody>
        <TableRow v-for="permission in sortedRows" :key="permission.id">
          <TableCell class="text-center">
            <Checkbox
              :disabled="!canAssign"
              :model-value="selectedPermissionNames.includes(permission.name)"
              @update:model-value="
                (value) => $emit('toggle-permission', permission.name, value)
              "
            />
          </TableCell>
          <TableCell class="text-muted-foreground">
            {{ toTitleCase(permission.group) }}
          </TableCell>
          <TableCell class="font-medium">
            {{ toTitleCase(permission.suffix) }}
          </TableCell>
          <TableCell class="hidden text-muted-foreground xl:table-cell">
            {{ permission.name }}
          </TableCell>
        </TableRow>
        <TableRow v-if="!sortedRows.length">
          <TableCell colspan="4" class="text-center text-muted-foreground">
            No permissions match the current filters.
          </TableCell>
        </TableRow>
      </TableBody>
    </Table>

    <p v-if="error" class="px-6 py-3 text-xs opacity-80">
      {{ error }}
    </p>
  </Card>
</template>
