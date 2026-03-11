<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import Button from '@/components/ui/button/Button.vue';
import DropdownMenu from '@/components/ui/dropdown-menu/DropdownMenu.vue';
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
  CheckIcon,
  FunnelIcon,
  SquareIcon,
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

const emit = defineEmits<{
  (event: 'apply-filters', column: string, values: string[]): void;
  (event: 'clear-filters', column: string): void;
  (event: 'toggle-sort', column: string): void;
}>();

const menuOpen = ref(false);
const draftFilters = ref<string[]>([...props.selectedFilters]);

const syncDraftFilters = () => {
  draftFilters.value = [...props.selectedFilters];
};

watch(
  () => props.selectedFilters,
  () => {
    if (!menuOpen.value) {
      syncDraftFilters();
    }
  },
  { deep: true },
);

watch(menuOpen, (open) => {
  if (!open) {
    syncDraftFilters();
  }
});

const sortIcon = computed(() => {
  if (props.sortDirection === 'asc') {
    return ArrowDownNarrowWideIcon;
  }

  if (props.sortDirection === 'desc') {
    return ArrowDownWideNarrowIcon;
  }

  return ArrowDownUpIcon;
});

const filterButtonTitle = computed(() => {
  if (props.selectedFilters.length === 0) {
    return `Filter ${props.label}. No filters selected.`;
  }

  return `Filter ${props.label}. ${props.selectedFilters.length} filter${props.selectedFilters.length === 1 ? '' : 's'} selected.`;
});

const sortButtonTitle = computed(() => {
  if (props.sortDirection === 'asc') {
    return `Sort ${props.label}. Currently ascending.`;
  }

  if (props.sortDirection === 'desc') {
    return `Sort ${props.label}. Currently descending.`;
  }

  return `Sort ${props.label}. Currently not sorted.`;
});

const hasDraftChanges = computed(() => {
  if (draftFilters.value.length !== props.selectedFilters.length) {
    return true;
  }

  return draftFilters.value.some(
    (value) => !props.selectedFilters.includes(value),
  );
});

const hasActiveFilters = computed(() => props.selectedFilters.length > 0);

const toggleDraftFilter = (value: string) => {
  if (draftFilters.value.includes(value)) {
    draftFilters.value = draftFilters.value.filter(
      (currentValue) => currentValue !== value,
    );

    return;
  }

  draftFilters.value = [...draftFilters.value, value];
};

const applyFilters = () => {
  emit('apply-filters', props.column, [...draftFilters.value].sort());
  menuOpen.value = false;
};

const clearFilters = () => {
  emit('clear-filters', props.column);
  menuOpen.value = false;
};
</script>

<template>
  <TableHead :class="cn('align-middle', headClass)">
    <div class="flex items-center gap-2">
      <span class="text-sm font-medium leading-none">{{ label }}</span>

      <DropdownMenu v-model:open="menuOpen">
        <DropdownMenuTrigger :as-child="true">
          <Button
            :appearance="hasActiveFilters ? 'filled' : 'outline'"
            size="sm"
            :aria-label="filterButtonTitle"
            :title="filterButtonTitle"
            class="h-6 gap-1 px-1.5 align-middle"
          >
            <FunnelIcon
              :class="
                cn(
                  'size-3',
                  hasActiveFilters
                    ? 'text-primary-foreground'
                    : 'text-muted-foreground',
                )
              "
            />
            <span
              v-if="selectedFilters.length"
              :class="
                cn(
                  'text-[11px]',
                  hasActiveFilters
                    ? 'text-primary-foreground'
                    : 'text-muted-foreground',
                )
              "
            >
              {{ selectedFilters.length }}
            </span>
          </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent align="start" class="w-64">
          <DropdownMenuLabel>{{ label }}</DropdownMenuLabel>
          <DropdownMenuItem
            :disabled="selectedFilters.length === 0"
            @select.prevent="clearFilters"
          >
            Clear filters
          </DropdownMenuItem>
          <DropdownMenuSeparator />
          <DropdownMenuItem
            v-for="option in filterOptions"
            :key="`${column}-${option}`"
            @select.prevent="toggleDraftFilter(option)"
          >
            <span
              class="flex size-4 items-center justify-center rounded-xs border border-border/80 bg-background text-primary"
            >
              <CheckIcon v-if="draftFilters.includes(option)" class="size-3" />
              <SquareIcon v-else class="size-3 text-transparent" />
            </span>
            {{ formatOptionLabel(option) }}
          </DropdownMenuItem>
          <DropdownMenuSeparator />
          <div class="flex justify-end p-1">
            <Button
              v-if="hasDraftChanges"
              appearance="filled"
              size="sm"
              type="button"
              @click="applyFilters"
            >
              Apply
            </Button>
          </div>
        </DropdownMenuContent>
      </DropdownMenu>

      <Button
        :appearance="sortDirection === 'none' ? 'outline' : 'filled'"
        size="sm"
        :aria-label="sortButtonTitle"
        :title="sortButtonTitle"
        class="h-6 px-1.5 align-middle"
        @click="$emit('toggle-sort', column)"
      >
        <component
          :is="sortIcon"
          :class="
            cn(
              'size-3',
              sortDirection === 'none'
                ? 'text-muted-foreground'
                : 'text-primary-foreground',
            )
          "
        />
      </Button>
    </div>
  </TableHead>
</template>
