<script setup lang="ts">
  import type { Component, HTMLAttributes } from "vue";
  import { computed, useAttrs, useSlots } from "vue";
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
      supportingText?: string;
      errorText?: string;
      message?: string;
      variant?: Variant;
      state?: State;
      error?: boolean;
      required?: boolean;
      noAsterisk?: boolean;
      prefixText?: string;
      suffixText?: string;
      leadingIcon?: Component | string;
      trailingIcon?: Component | string;
      placeholder?: string;
      type?: string;
      multiline?: boolean;
      rows?: number;
      cols?: number;
      maxLength?: number;
      minLength?: number;
      disabled?: boolean;
      readonly?: boolean;
    }>(),
    {
      variant: "filled",
      state: "default",
      type: "text",
      rows: 3,
    },
  );

  const emits = defineEmits<{
    (e: "update:modelValue", payload: string | number): void;
  }>();

  const attrs = useAttrs();
  const slots = useSlots();

  const modelValue = useVModel(props, "modelValue", emits, {
    passive: true,
    defaultValue: props.defaultValue,
  });

  const isOutlined = computed(() => props.variant === "outlined");
  const isMultiline = computed(() => props.multiline || props.type === "textarea");
  const hasLabel = computed(() => Boolean(props.label));

  const hasLeadingIcon = computed(() => Boolean(props.leadingIcon) || Boolean(slots["leading-icon"]));
  const hasTrailingIcon = computed(() => Boolean(props.trailingIcon) || Boolean(slots["trailing-icon"]));
  const hasPrefix = computed(() => Boolean(props.prefixText) || Boolean(slots.prefix));
  const hasSuffix = computed(() => Boolean(props.suffixText) || Boolean(slots.suffix));

  const leftPadding = computed(() => {
    if (hasLeadingIcon.value && hasPrefix.value) {
      return "pl-16";
    }

    if (hasLeadingIcon.value) {
      return "pl-12";
    }

    if (hasPrefix.value) {
      return "pl-10";
    }

    return "pl-4";
  });

  const rightPadding = computed(() => {
    if (hasTrailingIcon.value && hasSuffix.value) {
      return "pr-16";
    }

    if (hasTrailingIcon.value) {
      return "pr-12";
    }

    if (hasSuffix.value) {
      return "pr-10";
    }

    return "pr-4";
  });

  const labelOffset = computed(() => {
    if (hasLeadingIcon.value && hasPrefix.value) {
      return "left-16";
    }

    if (hasLeadingIcon.value) {
      return "left-12";
    }

    if (hasPrefix.value) {
      return "left-10";
    }

    return "left-4";
  });

  const prefixOffset = computed(() => (hasLeadingIcon.value ? "left-12" : "left-4"));
  const suffixOffset = computed(() => (hasTrailingIcon.value ? "right-12" : "right-4"));

  const hasError = computed(() => props.state === "error" || props.error);
  const hasSuccess = computed(() => props.state === "success");
  const showAsterisk = computed(() => props.required && !props.noAsterisk);

  const placeholderValue = computed(() => (props.label ? (props.placeholder ?? " ") : props.placeholder));
  const supportingText = computed(() => {
    if (hasError.value) {
      return props.errorText ?? props.message ?? props.supportingText;
    }

    return props.supportingText ?? props.message;
  });

  const showCounter = computed(() => typeof props.maxLength === "number");
  const currentLength = computed(() => String(modelValue.value ?? "").length);
  const inputId = computed(() => (attrs.id as string | undefined) ?? undefined);

  const baseFieldClasses = computed(() =>
    cn(
      "peer w-full min-w-0 rounded-[var(--radius-sm)] text-base leading-6 text-[var(--field-text)] transition-[border-color,box-shadow,background-color,color] outline-none disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50",
      hasLabel.value ? "pt-6 pb-2" : "py-4",
      leftPadding.value,
      rightPadding.value,
      isOutlined.value ? "border border-[color:var(--outline)] bg-transparent" : "border-b border-[color:var(--outline)] bg-[var(--field-bg)]",
      "focus-visible:border-[color:var(--field-focus)] focus-visible:ring-2 focus-visible:ring-[color:var(--field-focus)]",
      hasError.value ? "border-[color:var(--error)] ring-[color:var(--error)]" : "",
      hasSuccess.value ? "border-[color:var(--brand-mint)]" : "",
      isMultiline.value ? "min-h-[3.5rem] resize-y" : "h-14",
      props.class,
    ),
  );

  const labelClasses = computed(() =>
    cn(
      "absolute top-4 origin-left text-sm text-[var(--field-label)] transition-[transform,color,top,background-color] duration-150",
      labelOffset.value,
      "peer-placeholder-shown:top-4 peer-placeholder-shown:scale-100 peer-focus:top-2 peer-focus:scale-90 peer-[&:not(:placeholder-shown)]:top-2 peer-[&:not(:placeholder-shown)]:scale-90",
      "peer-focus:text-[var(--field-focus)]",
      "peer-disabled:opacity-60",
      isOutlined.value ? "bg-[var(--surface)] px-1" : "bg-[var(--field-bg)] px-1",
      hasError.value ? "text-[var(--error)] peer-focus:text-[var(--error)]" : "",
      hasSuccess.value ? "text-[color:var(--brand-mint)] peer-focus:text-[color:var(--brand-mint)]" : "",
    ),
  );

  const assistiveTextClasses = computed(() => cn("text-xs", hasError.value ? "text-[var(--error)]" : "text-[var(--field-label)]", hasSuccess.value ? "text-[color:var(--brand-mint)]" : ""));
</script>

<template>
  <div class="w-full">
    <div class="relative">
      <span v-if="hasLeadingIcon" class="absolute left-4 top-1/2 flex -translate-y-1/2 items-center text-[var(--field-label)] transition-colors duration-150 peer-focus:text-[var(--field-focus)] peer-disabled:opacity-60">
        <slot name="leading-icon">
          <component v-if="leadingIcon && typeof leadingIcon !== 'string'" :is="leadingIcon" class="size-5" />
          <span v-else-if="leadingIcon" class="text-sm font-medium">{{ leadingIcon }}</span>
        </slot>
      </span>

      <span v-if="hasPrefix" :class="cn('absolute top-1/2 flex -translate-y-1/2 items-center text-sm text-[var(--field-label)] peer-disabled:opacity-60', prefixOffset)">
        <slot name="prefix">
          {{ prefixText }}
        </slot>
      </span>

      <component
        :is="isMultiline ? 'textarea' : 'input'"
        v-model="modelValue"
        data-slot="input"
        :id="inputId"
        :type="isMultiline ? undefined : type"
        :rows="isMultiline ? rows : undefined"
        :cols="isMultiline ? cols : undefined"
        :placeholder="placeholderValue"
        :maxlength="maxLength"
        :minlength="minLength"
        :required="required"
        :disabled="disabled"
        :readonly="readonly"
        :aria-invalid="hasError ? 'true' : undefined"
        :class="baseFieldClasses"
        v-bind="attrs"
      />

      <span v-if="hasTrailingIcon" class="absolute right-4 top-1/2 flex -translate-y-1/2 items-center text-[var(--field-label)] transition-colors duration-150 peer-focus:text-[var(--field-focus)] peer-disabled:opacity-60">
        <slot name="trailing-icon">
          <component v-if="trailingIcon && typeof trailingIcon !== 'string'" :is="trailingIcon" class="size-5" />
          <span v-else-if="trailingIcon" class="text-sm font-medium">{{ trailingIcon }}</span>
        </slot>
      </span>

      <span v-if="hasSuffix" :class="cn('absolute top-1/2 flex -translate-y-1/2 items-center text-sm text-[var(--field-label)] peer-disabled:opacity-60', suffixOffset)">
        <slot name="suffix">
          {{ suffixText }}
        </slot>
      </span>

      <label v-if="label" :for="inputId" :class="labelClasses">
        {{ label }}
        <span v-if="showAsterisk" class="text-[var(--error)]"> *</span>
      </label>
    </div>

    <div v-if="supportingText || showCounter" class="mt-2 flex items-start justify-between gap-3">
      <p v-if="supportingText" :class="assistiveTextClasses">
        {{ supportingText }}
      </p>
      <p v-if="showCounter" :class="assistiveTextClasses">{{ currentLength }} / {{ maxLength }}</p>
    </div>
  </div>
</template>
