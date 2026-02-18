<script setup lang="ts">
  import { computed, ref, watch } from "vue";
  import { Input } from "@/components/ui/input";
  import type { InputVariants } from "@/components/ui/input";
  import { toSnakeCase, toTitleCase } from "@/lib/utils";

  const defaultGroups = [
    { value: "users", label: "Users" },
    { value: "roles", label: "Roles" },
    { value: "permissions", label: "Permissions" },
  ];

  const props = withDefaults(
    defineProps<{
      modelValue: string;
      disabled?: boolean;
      error?: string;
      label?: string;
      id?: string;
      variant?: InputVariants["variant"];
      groups?: string[];
    }>(),
    {
      label: "Group",
      id: "permission-group",
      variant: "outlined",
      groups: () => [],
    },
  );

  const emit = defineEmits<{
    (event: "update:modelValue", value: string): void;
  }>();

  const modelValue = computed({
    get: () => props.modelValue,
    set: (value: string) => emit("update:modelValue", value),
  });

  const isOpen = ref(false);
  const searchQuery = ref("");

  const groupSuggestions = computed(() => {
    return Array.from(new Set([...defaultGroups.map((group) => group.value), ...props.groups.map((group) => toSnakeCase(group))]))
      .filter(Boolean)
      .sort();
  });

  const normalizedQuery = computed(() => toSnakeCase(searchQuery.value));

  const filteredSuggestions = computed(() => {
    if (!normalizedQuery.value) {
      return groupSuggestions.value;
    }

    return groupSuggestions.value.filter((group) => group.includes(normalizedQuery.value));
  });

  const openSuggestions = () => {
    isOpen.value = true;
    searchQuery.value = "";
  };

  const closeSuggestions = () => {
    setTimeout(() => {
      isOpen.value = false;
    }, 100);
  };

  const selectGroup = (group: string) => {
    modelValue.value = group;
    searchQuery.value = "";
    isOpen.value = false;
  };

  const handleInputUpdate = (value: string) => {
    modelValue.value = value;
    searchQuery.value = value;
    isOpen.value = true;
  };

  watch(
    () => props.modelValue,
    () => {
      if (!isOpen.value) {
        searchQuery.value = "";
      }
    },
  );
</script>

<template>
  <div class="relative">
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

    <div v-if="isOpen && filteredSuggestions.length > 0" class="absolute z-30 mt-1 max-h-56 w-full overflow-y-auto rounded-xl border border-[color:var(--outline)] bg-[var(--surface)] p-1 shadow-[var(--elevation-2)]">
      <button v-for="group in filteredSuggestions" :key="group" type="button" class="flex w-full items-center rounded-lg px-3 py-2 text-left text-sm hover:bg-muted/70" @mousedown.prevent="selectGroup(group)">
        <span>{{ toTitleCase(group) }}</span>
        <span class="ml-auto text-xs opacity-70">{{ group }}</span>
      </button>
    </div>
  </div>
</template>
