<script setup lang="ts">
import { computed } from 'vue';
import Button from '@/components/ui/button/Button.vue';
import DropdownMenu from '@/components/ui/dropdown-menu/DropdownMenu.vue';
import DropdownMenuCheckboxItem from '@/components/ui/dropdown-menu/DropdownMenuCheckboxItem.vue';
import DropdownMenuContent from '@/components/ui/dropdown-menu/DropdownMenuContent.vue';
import DropdownMenuItem from '@/components/ui/dropdown-menu/DropdownMenuItem.vue';
import DropdownMenuLabel from '@/components/ui/dropdown-menu/DropdownMenuLabel.vue';
import DropdownMenuSeparator from '@/components/ui/dropdown-menu/DropdownMenuSeparator.vue';
import DropdownMenuTrigger from '@/components/ui/dropdown-menu/DropdownMenuTrigger.vue';
import TableHead from '@/components/ui/table/TableHead.vue';
import { cn } from '@/lib/utils';
import {
  ArrowDownNarrowWideIcon,
  ArrowDownUpIcon,
  ArrowDownWideNarrowIcon,
  ListFilterIcon,
} from 'lucide-vue-next';

const props = withDefaults(
  defineProps<{
    column: string;
    filterOptions: string[];
    formatOptionLabel?: (value: string) => string;
    headClass?: string;
    label: string;
    selectedFilters: string[];
    sortDirection: 'asc' | 'desc' | 'none';
  }>(),
  {
    formatOptionLabel: (value: string) => value,
    headClass: undefined,
  },
);

defineEmits<{
  (event: 'clear-filters', column: string): void;
  (
    event: 'toggle-filter',
    column: string,
    value: string,
    checked: boolean | 'indeterminate',
  ): void;
  (event: 'toggle-sort', column: string): void;
}>();

const sortIcon = computed(() => {
  if (props.sortDirection === 'asc') {
    return ArrowDownNarrowWideIcon;
  }

  if (props.sortDirection === 'desc') {
    return ArrowDownWideNarrowIcon;
  }

  return ArrowDownUpIcon;
});
</script>

<template>
  <TableHead :class="cn('align-top', headClass)">
    <div class="flex items-start gap-2">
      <span>{{ label }}</span>

      <DropdownMenu>
        <DropdownMenuTrigger :as-child="true">
          <Button
            appearance="outline"
            size="sm"
            class="h-7 gap-1 px-2 text-muted-foreground"
          >
            <ListFilterIcon class="size-3.5" />
            <span v-if="selectedFilters.length" class="text-[10px]">
              {{ selectedFilters.length }}
            </span>
          </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent align="start" class="w-64">
          <DropdownMenuLabel>{{ label }}</DropdownMenuLabel>
          <DropdownMenuItem
            :disabled="selectedFilters.length === 0"
            @select.prevent="$emit('clear-filters', column)"
          >
            Clear filters
          </DropdownMenuItem>
          <DropdownMenuSeparator />
          <DropdownMenuCheckboxItem
            v-for="option in filterOptions"
            :key="`${column}-${option}`"
            :checked="selectedFilters.includes(option)"
            @update:checked="
              (checked: boolean | 'indeterminate') =>
                $emit('toggle-filter', column, option, checked)
            "
          >
            {{ formatOptionLabel(option) }}
          </DropdownMenuCheckboxItem>
        </DropdownMenuContent>
      </DropdownMenu>

      <Button
        appearance="outline"
        size="sm"
        class="h-7 px-2 text-muted-foreground"
        @click="$emit('toggle-sort', column)"
      >
        <component :is="sortIcon" class="size-3.5" />
      </Button>
    </div>
  </TableHead>
</template>
