<script setup lang="ts">
import { computed, ref } from 'vue';
import AssignmentTableCard from '@/components/admin/AssignmentTableCard.vue';
import Checkbox from '@/components/ui/checkbox/Checkbox.vue';
import Input from '@/components/ui/input/Input.vue';
import Table from '@/components/ui/table/Table.vue';
import TableBody from '@/components/ui/table/TableBody.vue';
import TableCell from '@/components/ui/table/TableCell.vue';
import TableHead from '@/components/ui/table/TableHead.vue';
import TableHeader from '@/components/ui/table/TableHeader.vue';
import TableRow from '@/components/ui/table/TableRow.vue';
import { toTitleCase } from '@/lib/utils';
import type { RoleOption } from '@/types/page-props';

const props = defineProps<{
  canAssign: boolean;
  error?: string;
  processing: boolean;
  roles: RoleOption[];
  selectedRoleNames: string[];
}>();

const emit = defineEmits<{
  (event: 'save'): void;
  (event: 'toggle-role', roleName: string, value: boolean | 'indeterminate'): void;
}>();

const search = ref('');

const filteredRoles = computed(() => {
  const searchTerm = search.value.trim().toLowerCase();

  if (!searchTerm) {
    return props.roles;
  }

  return props.roles.filter((role) => role.name.toLowerCase().includes(searchTerm));
});

const resultsLabel = computed(() => {
  const count = filteredRoles.value.length;

  return `${count} role${count === 1 ? '' : 's'}`;
});
</script>

<template>
  <AssignmentTableCard
    :can-submit="canAssign"
    :error="error"
    :processing="processing"
    description="Filter and assign roles from one table."
    save-label="Save Roles"
    title="Roles"
    @save="$emit('save')"
  >
    <Table wrapper-class="rounded-none border-0">
      <TableHeader>
        <TableRow>
          <TableHead class="w-14 text-center">Access</TableHead>
          <TableHead>Display Name</TableHead>
          <TableHead>Slug</TableHead>
          <TableHead class="text-right text-xs font-semibold text-muted-foreground uppercase tracking-[0.16em]">
            {{ resultsLabel }}
          </TableHead>
        </TableRow>
        <TableRow>
          <TableHead />
          <TableHead class="align-top" colspan="2">
            <Input
              id="user-roles-search"
              v-model="search"
              placeholder="Filter roles..."
              variant="outlined"
            />
          </TableHead>
          <TableHead />
        </TableRow>
      </TableHeader>
      <TableBody>
        <TableRow v-for="role in filteredRoles" :key="role.id">
          <TableCell class="text-center">
            <Checkbox
              :disabled="!canAssign"
              :model-value="selectedRoleNames.includes(role.name)"
              @update:model-value="(value) => $emit('toggle-role', role.name, value)"
            />
          </TableCell>
          <TableCell class="font-medium">
            {{ toTitleCase(role.name) }}
          </TableCell>
          <TableCell class="text-xs font-medium text-muted-foreground italic">
            {{ role.name }}
          </TableCell>
          <TableCell />
        </TableRow>
        <TableRow v-if="!filteredRoles.length">
          <TableCell colspan="4" class="text-center text-muted-foreground">
            No roles match the current filter.
          </TableCell>
        </TableRow>
      </TableBody>
    </Table>
  </AssignmentTableCard>
</template>
