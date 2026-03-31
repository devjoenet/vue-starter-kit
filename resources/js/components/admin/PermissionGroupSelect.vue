<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import Input from '@/components/ui/input/Input.vue';
import type { InputVariants } from '@/components/ui/input/variants';
import { toSnakeCase, toTitleCase } from '@/lib/utils';
import type { PermissionGroupOption } from '@/types/admin/permissions';

const defaultGroups = [
  {
    slug: 'users',
    label: 'User Administration',
    description: 'Identity, lifecycle, and role assignment controls.',
    permissions_count: 0,
  },
  {
    slug: 'roles',
    label: 'Role Management',
    description: 'Role definitions and access-footprint maintenance.',
    permissions_count: 0,
  },
  {
    slug: 'permissions',
    label: 'Permission Catalog',
    description: 'Permission definitions and ACL catalog maintenance.',
    permissions_count: 0,
  },
];

const props = withDefaults(
  defineProps<{
    modelValue: string;
    disabled?: boolean;
    error?: string;
    label?: string;
    id?: string;
    variant?: InputVariants['variant'];
    groups?: PermissionGroupOption[];
  }>(),
  {
    label: 'Group',
    id: 'permission-group',
    variant: 'outlined',
    groups: () => [],
  },
);

const emit = defineEmits<{
  (event: 'update:modelValue', value: string): void;
}>();

const modelValue = computed({
  get: () => props.modelValue,
  set: (value: string) => emit('update:modelValue', value),
});

const isOpen = ref(false);
const searchQuery = ref('');

const groupSuggestions = computed(() => {
  const suggestions = new Map<string, PermissionGroupOption>();

  [...defaultGroups, ...props.groups].forEach((group) => {
    const slug = toSnakeCase(group.slug);

    if (!slug) {
      return;
    }

    suggestions.set(slug, {
      ...group,
      slug,
      label: group.label || toTitleCase(slug),
    });
  });

  return Array.from(suggestions.values()).sort((left, right) => left.label.localeCompare(right.label));
});

const normalizedQuery = computed(() => toSnakeCase(searchQuery.value));
const selectedGroup = computed(() => groupSuggestions.value.find((group) => group.slug === toSnakeCase(props.modelValue)));

const filteredSuggestions = computed(() => {
  if (!normalizedQuery.value) {
    return groupSuggestions.value;
  }

  return groupSuggestions.value.filter((group) => [group.slug, group.label, group.description ?? ''].join(' ').toLowerCase().includes(normalizedQuery.value.toLowerCase()));
});

const openSuggestions = () => {
  isOpen.value = true;
  searchQuery.value = '';
};

const closeSuggestions = () => {
  setTimeout(() => {
    isOpen.value = false;
  }, 100);
};

const selectGroup = (group: PermissionGroupOption) => {
  modelValue.value = group.slug;
  searchQuery.value = '';
  isOpen.value = false;
};

const handleInputUpdate = (value: string | number) => {
  const normalizedValue = String(value);

  modelValue.value = normalizedValue;
  searchQuery.value = normalizedValue;
  isOpen.value = true;
};

watch(
  () => props.modelValue,
  () => {
    if (!isOpen.value) {
      searchQuery.value = '';
    }
  },
);
</script>

<template>
  <div class="relative space-y-2">
    <Input
      :id="id"
      :model-value="modelValue"
      @update:model-value="handleInputUpdate"
      :disabled="disabled"
      :error="Boolean(error)"
      :error-text="error"
      :label="label"
      :variant="variant"
      placeholder="Select or type a group"
      autocomplete="off"
      @focus="openSuggestions"
      @blur="closeSuggestions"
    />

    <div v-if="isOpen && filteredSuggestions.length > 0" class="absolute z-30 mt-1 max-h-56 w-full overflow-y-auto rounded-xl border border-(--outline) bg-(--surface) p-1 shadow-(--elevation-2)">
      <button v-for="group in filteredSuggestions" :key="group.slug" type="button" class="w-full rounded-lg px-3 py-3 text-left transition-colors hover:bg-muted/70" @mousedown.prevent="selectGroup(group)">
        <span class="flex items-start justify-between gap-4">
          <span class="min-w-0 space-y-1">
            <span class="block text-sm font-semibold">{{ group.label }}</span>
            <span v-if="group.description" class="block text-xs leading-5 text-muted-foreground">
              {{ group.description }}
            </span>
          </span>

          <span class="shrink-0 text-right text-[0.7rem] tracking-[0.18em] text-muted-foreground uppercase">
            <span class="block">{{ group.slug }}</span>
            <span class="block">{{ group.permissions_count }} permission<span v-if="group.permissions_count !== 1">s</span></span>
          </span>
        </span>
      </button>
    </div>

    <p class="text-xs leading-5 text-muted-foreground">
      {{ selectedGroup?.description ?? 'Use groups to organize permissions into a shared catalog across internal and customer-facing access areas.' }}
    </p>
  </div>
</template>
