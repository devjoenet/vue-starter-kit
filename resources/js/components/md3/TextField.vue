<script setup lang="ts">
  import { computed } from "vue";

  const props = withDefaults(
    defineProps<{
      modelValue: string;
      label: string;
      type?: string;
      variant?: "filled" | "outlined";
      state?: "default" | "error" | "success";
      disabled?: boolean;
      message?: string;
    }>(),
    {
      type: "text",
      variant: "outlined",
      state: "default",
      disabled: false,
    },
  );

  const emit = defineEmits<{ (e: "update:modelValue", v: string): void }>();

  const rootClass = computed(() => ["md3-field", `md3-field--${props.variant}`, `md3-field--${props.state}`, props.disabled ? "opacity-60 pointer-events-none" : ""].join(" "));
</script>

<template>
  <div class="space-y-1">
    <div :class="rootClass">
      <input :type="type" class="md3-field__input" :disabled="disabled" :value="modelValue" placeholder=" " @input="emit('update:modelValue', ($event.target as HTMLInputElement).value)" />
      <label class="md3-field__label">{{ label }}</label>
    </div>

    <p v-if="message" class="text-xs md3-field__message">{{ message }}</p>
  </div>
</template>
