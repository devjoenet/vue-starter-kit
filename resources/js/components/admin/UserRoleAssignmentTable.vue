<script setup lang="ts">
import AssignmentTableCard from '@/components/admin/AssignmentTableCard.vue';
import Checkbox from '@/components/ui/checkbox/Checkbox.vue';
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
  roles: RoleOption[];
  selectedRoleNames: string[];
}>();

defineEmits<{
  (
    event: 'toggle-role',
    roleName: string,
    value: boolean | 'indeterminate',
  ): void;
}>();
</script>

<template>
  <AssignmentTableCard
    :error="error"
    description="Filter and assign roles from one table."
    title="Roles"
  >
    <Table wrapper-class="rounded-none border-0">
      <TableHeader>
        <TableRow>
          <TableHead class="w-14 text-center">Access</TableHead>
          <TableHead>Display Name</TableHead>
          <TableHead class="hidden md:table-cell">Slug</TableHead>
        </TableRow>
      </TableHeader>
      <TableBody>
        <TableRow v-for="role in props.roles" :key="role.id">
          <TableCell class="text-center">
            <Checkbox
              :disabled="!canAssign"
              :model-value="selectedRoleNames.includes(role.name)"
              @update:model-value="
                (value) => $emit('toggle-role', role.name, value)
              "
            />
          </TableCell>
          <TableCell class="font-medium">
            {{ toTitleCase(role.name) }}
          </TableCell>
          <TableCell
            class="hidden text-xs font-medium text-muted-foreground italic md:table-cell"
          >
            {{ role.name }}
          </TableCell>
        </TableRow>
        <TableRow v-if="props.roles.length === 0">
          <TableCell colspan="3" class="text-center text-muted-foreground">
            No roles are available.
          </TableCell>
        </TableRow>
      </TableBody>
    </Table>
  </AssignmentTableCard>
</template>
