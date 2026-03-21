<script setup lang="ts">
import { ArrowDownNarrowWideIcon, ArrowDownWideNarrowIcon, ArrowUpDownIcon, CheckIcon, FunnelIcon, SquareIcon } from 'lucide-vue-next';
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

const props = withDefaults(
  defineProps<{
    as?: 'table' | 'toolbar';
    column: string;
    filterOptions: string[];
    formatOptionLabel?: (value: string) => string;
    headClass?: string;
    label: string;
    selectedFilters: string[];
    sortDirection: 'asc' | 'desc' | 'none';
  }>(),
  {
    as: 'table',
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

const filterButtonTitle = computed(() => {
  if (props.selectedFilters.length === 0) {
    return `Filter by ${props.label}`;
  }

  return `Filter by ${props.label} \n${props.selectedFilters.length} filter${props.selectedFilters.length === 1 ? '' : 's'} selected.`;
});

const sortButtonTitle = computed(() => {
  if (props.sortDirection === 'asc') {
    return `${props.label} sorted in ascending order.`;
  }

  if (props.sortDirection === 'desc') {
    return `${props.label} sorted in descending order.`;
  }

  return `${props.label} is not sorted.`;
});

const hasDraftChanges = computed(() => {
  if (draftFilters.value.length !== props.selectedFilters.length) {
    return true;
  }

  return draftFilters.value.some((value) => !props.selectedFilters.includes(value));
});

const hasActiveFilters = computed(() => props.selectedFilters.length > 0);

const rootComponent = computed(() => (props.as === 'toolbar' ? 'div' : TableHead));

const rootClasses = computed(() => (props.as === 'toolbar' ? 'rounded-[var(--radius-lg)] border border-border/70 bg-background/88 p-3 shadow-[var(--elevation-1)]' : cn('align-middle', props.headClass)));

const contentClasses = computed(() => (props.as === 'toolbar' ? 'flex items-start justify-between gap-3' : 'flex items-center gap-2'));

const helperText = computed(() => {
  const states: string[] = [];

  if (props.selectedFilters.length > 0) {
    states.push(`${props.selectedFilters.length} filter${props.selectedFilters.length === 1 ? '' : 's'} active`);
  }

  if (props.sortDirection !== 'none') {
    states.push(props.sortDirection === 'asc' ? 'sorted ascending' : 'sorted descending');
  }

  return states.join(' · ') || 'Filter or sort this column';
});

const toggleDraftFilter = (value: string) => {
  if (draftFilters.value.includes(value)) {
    draftFilters.value = draftFilters.value.filter((currentValue) => currentValue !== value);

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
  <component :is="rootComponent" :class="rootClasses">
    <div :class="contentClasses">
      <div class="min-w-0">
        <span class="text-sm leading-none font-medium">{{ label }}</span>
        <p v-if="props.as === 'toolbar'" class="mt-1 text-xs leading-5 text-muted-foreground">
          {{ helperText }}
        </p>
      </div>

      <div class="flex shrink-0 items-center gap-2">
        <DropdownMenu v-model:open="menuOpen">
          <DropdownMenuTrigger :as-child="true">
            <Button
              size="iconSm"
              rounded="full"
              :appearance="hasActiveFilters ? 'filled' : 'outline'"
              :variant="hasActiveFilters ? 'primary' : 'muted'"
              :aria-label="filterButtonTitle"
              :title="filterButtonTitle"
              class="relative shrink-0 align-middle"
            >
              <FunnelIcon :class="[hasActiveFilters ? 'stroke-primary-foreground' : 'stroke-muted-foreground', 'size-3']" />
              <span
                v-if="selectedFilters.length"
                :class="cn('absolute -top-1 -right-1 flex min-w-3 items-center justify-center rounded-full p-0.5 text-[8px] leading-none', hasActiveFilters ? 'bg-primary text-primary-foreground' : 'bg-transparent text-transparent')"
              >
                {{ selectedFilters.length }}
              </span>
            </Button>
          </DropdownMenuTrigger>
          <DropdownMenuContent align="start" class="w-72">
            <DropdownMenuLabel>{{ label }}</DropdownMenuLabel>
            <DropdownMenuItem variant="primary" :disabled="selectedFilters.length === 0" @select.prevent="clearFilters"> Clear filters </DropdownMenuItem>
            <DropdownMenuSeparator />
            <DropdownMenuItem v-for="option in filterOptions" :key="`${column}-${option}`" variant="primary" @select.prevent="toggleDraftFilter(option)">
              <span class="flex size-4 items-center justify-center rounded-xs border border-border/80 bg-background text-primary">
                <CheckIcon v-if="draftFilters.includes(option)" class="size-3" />
                <SquareIcon v-else class="size-3 text-transparent" />
              </span>
              {{ formatOptionLabel(option) }}
            </DropdownMenuItem>
            <DropdownMenuSeparator />
            <div class="flex justify-end p-1">
              <Button v-if="hasDraftChanges" size="sm" type="button" @click="applyFilters"> Apply </Button>
            </div>
          </DropdownMenuContent>
        </DropdownMenu>

        <Button
          size="iconSm"
          rounded="full"
          :appearance="props.sortDirection === 'none' ? 'outline' : 'filled'"
          :variant="props.sortDirection === 'none' ? 'muted' : 'primary'"
          :aria-label="sortButtonTitle"
          :title="sortButtonTitle"
          class="shrink-0 align-middle"
          @click="$emit('toggle-sort', column)"
        >
          <ArrowDownNarrowWideIcon v-if="props.sortDirection === 'asc'" class="size-3 stroke-primary-foreground" />
          <ArrowDownWideNarrowIcon v-if="props.sortDirection === 'desc'" class="size-3 stroke-primary-foreground" />
          <ArrowUpDownIcon v-if="props.sortDirection === 'none'" class="size-3 stroke-muted-foreground" />
        </Button>
      </div>
    </div>
  </component>
</template>
