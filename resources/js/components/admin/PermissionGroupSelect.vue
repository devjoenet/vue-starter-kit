<script setup lang="ts">
  import type { HTMLAttributes } from "vue";
  import { computed } from "vue";
  import type { InputVariants } from "@/components/ui/input";
  import { Select } from "@/components/ui/select";
  import type { SelectOption, SelectOptionVariant } from "@/components/ui/select";

  const groupOptions: SelectOption[] = [
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
      optionVariant?: SelectOptionVariant;
      optionClass?: HTMLAttributes["class"];
      contentClass?: HTMLAttributes["class"];
    }>(),
    {
      label: "Group",
      id: "permission-group",
      variant: "outlined",
      optionVariant: "primary",
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
  <Select :id="id" v-model="modelValue" :options="groupOptions" :disabled="disabled" :error="Boolean(error)" :error-text="error" :label="label" :variant="variant" :option-variant="optionVariant" :option-class="optionClass" :content-class="contentClass" />
</template>
