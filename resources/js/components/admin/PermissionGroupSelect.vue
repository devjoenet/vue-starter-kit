<script setup lang="ts">
  import { computed } from "vue";
  import { cn } from "@/lib/utils";

  const props = withDefaults(
    defineProps<{
      modelValue: string;
      disabled?: boolean;
      error?: string;
      label?: string;
      id?: string;
    }>(),
    {
      label: "Group",
      id: "permission-group",
    },
  );

  const emit = defineEmits<{
    (event: "update:modelValue", value: string): void;
  }>();

  const modelValue = computed({
    get: () => props.modelValue,
    set: (value: string) => emit("update:modelValue", value),
  });
</script>

<template>
  <div class="space-y-1">
    <div class="text-sm font-medium opacity-80">{{ label }}</div>
    <select
      :id="id"
      v-model="modelValue"
      :disabled="disabled"
      :class="
        cn(
          'h-14 w-full rounded-[var(--radius-sm)] border border-[color:var(--outline)] bg-[var(--field-bg)] px-4 text-sm text-[var(--field-text)] outline-none transition-[border-color,box-shadow] focus-visible:border-[color:var(--field-focus)] focus-visible:ring-2 focus-visible:ring-[color:var(--field-focus)]',
          error ? 'border-destructive focus-visible:border-destructive focus-visible:ring-destructive' : '',
        )
      "
    >
      <option value="users">users</option>
      <option value="roles">roles</option>
      <option value="permissions">permissions</option>
    </select>
    <p v-if="error" class="text-xs text-destructive">
      {{ error }}
    </p>
  </div>
</template>
