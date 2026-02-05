<script setup lang="ts">
  import type { HTMLAttributes } from "vue";
  import { computed, useAttrs } from "vue";
  import { useVModel } from "@vueuse/core";
  import { cn } from "@/lib/utils";

  type Variant = "filled" | "outlined";
  type State = "default" | "error" | "success";

  const props = withDefaults(
    defineProps<{
      defaultValue?: string | number;
      modelValue?: string | number;
      class?: HTMLAttributes["class"];
      label?: string;
      message?: string;
      variant?: Variant;
      state?: State;
    }>(),
    {
      variant: "filled",
      state: "default",
    },
  );

  const emits = defineEmits<{
    (e: "update:modelValue", payload: string | number): void;
  }>();

  const attrs = useAttrs();

  const modelValue = useVModel(props, "modelValue", emits, {
    passive: true,
    defaultValue: props.defaultValue,
  });

  const isOutlined = computed(() => props.variant === "outlined");

  const inputClasses = computed(() =>
    cn(
      "peer h-14 w-full min-w-0 rounded-[var(--radius-sm)] border px-4 pt-6 pb-2 text-base leading-6 text-[var(--field-text)] transition-[border-color,box-shadow,background-color] outline-none placeholder:text-transparent disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50",
      isOutlined.value ? "border-[color:var(--outline)] bg-transparent" : "border-transparent bg-[var(--field-bg)]",
      "focus-visible:border-[color:var(--field-focus)] focus-visible:ring-2 focus-visible:ring-[color:var(--field-focus)]",
      props.state === "error" ? "border-[color:var(--error)] ring-[color:var(--error)]" : "",
      props.state === "success" ? "border-[color:var(--success)]" : "",
      props.class,
    ),
  );

  const labelClasses = computed(() =>
    cn(
      "absolute left-4 top-4 origin-left text-sm text-[var(--field-label)] transition-[transform,color,top] duration-150",
      "peer-placeholder-shown:top-4 peer-placeholder-shown:scale-100 peer-focus:top-2 peer-focus:scale-90 peer-[&:not(:placeholder-shown)]:top-2 peer-[&:not(:placeholder-shown)]:scale-90",
      "peer-focus:text-[var(--field-focus)]",
      props.state === "error" ? "text-[var(--error)] peer-focus:text-[var(--error)]" : "",
      props.state === "success" ? "text-[var(--success)] peer-focus:text-[var(--success)]" : "",
    ),
  );
</script>

<template>
  <div class="w-full">
    <div v-if="label" class="relative">
      <input v-model="modelValue" data-slot="input" :placeholder="' '" :class="inputClasses" v-bind="attrs" />
      <label :class="labelClasses">{{ label }}</label>
    </div>

    <input
      v-else
      v-model="modelValue"
      data-slot="input"
      :class="
        cn(
          'h-14 w-full min-w-0 rounded-[var(--radius-sm)] border px-4 py-4 text-base leading-6 text-[var(--field-text)] transition-[border-color,box-shadow,background-color] outline-none placeholder:text-[var(--field-label)] disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50',
          isOutlined ? 'border-[color:var(--outline)] bg-transparent' : 'border-transparent bg-[var(--field-bg)]',
          'focus-visible:border-[color:var(--field-focus)] focus-visible:ring-2 focus-visible:ring-[color:var(--field-focus)]',
          props.state === 'error' ? 'border-[color:var(--error)] ring-[color:var(--error)]' : '',
          props.state === 'success' ? 'border-[color:var(--success)]' : '',
          props.class,
        )
      "
      v-bind="attrs"
    />

    <p v-if="message" class="mt-2 text-xs text-[var(--error)]">
      {{ message }}
    </p>
  </div>
</template>
