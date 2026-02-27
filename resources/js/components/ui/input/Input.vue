<script setup lang="ts">
  import type { Component, HTMLAttributes } from "vue";
  import { computed, ref, useAttrs, useSlots, watch } from "vue";
  import { useVModel } from "@vueuse/core";
  import { CircleX } from "lucide-vue-next";
  import { cn } from "@/lib/utils";
  import type { InputVariants } from "./styles";
  import { inputAssistiveTextVariants, inputLabelVariants, inputVariants } from "./styles";

  defineOptions({
    inheritAttrs: false,
  });

  const props = withDefaults(
    defineProps<{
      defaultValue?: string | number;
      modelValue?: string | number;
      modelModifiers?: { trim?: boolean };
      class?: HTMLAttributes["class"];
      label?: string;
      supportingText?: string;
      errorText?: string;
      message?: string;
      variant?: InputVariants["variant"];
      state?: InputVariants["state"];
      error?: boolean;
      required?: boolean;
      noAsterisk?: boolean;
      prefixText?: string;
      suffixText?: string;
      leadingIcon?: Component | string;
      trailingIcon?: Component | string;
      clearIcon?: Component;
      placeholder?: string;
      type?: string;
      multiline?: boolean;
      textarea?: boolean;
      rows?: number;
      cols?: number;
      maxLength?: number;
      minLength?: number;
      disabled?: boolean;
      readonly?: boolean;
      clearable?: boolean;
      resize?: boolean;
      preventAutoFill?: boolean;
      autofocus?: boolean;
    }>(),
    {
      variant: "filled",
      state: "default",
      type: "text",
      rows: 3,
      modelModifiers: () => ({}),
      preventAutoFill: true,
      resize: true,
      clearable: false,
      textarea: false,
    },
  );

  const emits = defineEmits<{
    (e: "update:modelValue", payload: string | number): void;
    (e: "focus", payload: FocusEvent): void;
    (e: "blur", payload: FocusEvent): void;
    (e: "input", payload: { value: string; event: Event }): void;
    (e: "change", payload: { value: string; event: Event }): void;
    (e: "clear", payload: string): void;
  }>();

  const attrs = useAttrs();
  const slots = useSlots();
  const fieldRef = ref<HTMLInputElement | HTMLTextAreaElement | null>(null);
  const isComposing = ref(false);

  const modelValue = useVModel(props, "modelValue", emits, {
    passive: true,
    defaultValue: props.defaultValue,
  });

  watch(
    () => props.defaultValue,
    (value) => {
      if (props.modelValue !== undefined) {
        return;
      }

      modelValue.value = value ?? "";
    },
  );

  const isMultiline = computed(() => props.multiline || props.textarea || props.type === "textarea");
  const hasLabel = computed(() => Boolean(props.label));

  const hasLeadingIcon = computed(() => Boolean(props.leadingIcon) || Boolean(slots["leading-icon"]) || Boolean(slots["prepend-icon"]));
  const hasTrailingIcon = computed(() => Boolean(props.trailingIcon) || Boolean(slots["trailing-icon"]) || Boolean(slots["append-icon"]));
  const hasPrefix = computed(() => Boolean(props.prefixText) || Boolean(slots.prefix));
  const hasSuffix = computed(() => Boolean(props.suffixText) || Boolean(slots.suffix));

  const hasError = computed(() => props.state === "error" || props.state === "destructive" || props.error);
  const hasInfo = computed(() => props.state === "info");
  const hasWarning = computed(() => props.state === "warning");
  const hasSuccess = computed(() => props.state === "success");

  const fieldState = computed<InputVariants["state"]>(() => {
    if (hasError.value) {
      if (props.state === "destructive") {
        return "destructive";
      }

      return "error";
    }

    if (hasInfo.value) {
      return "info";
    }

    if (hasWarning.value) {
      return "warning";
    }

    if (hasSuccess.value) {
      return "success";
    }

    return "default";
  });

  const showAsterisk = computed(() => props.required && !props.noAsterisk);
  const normalizedType = computed(() => (props.type === "number" ? "text" : props.type));
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

  const hasClearControl = computed(() => props.clearable && !props.disabled && !props.readonly && String(modelValue.value ?? "").length > 0);

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

  const rightDecorators = computed(() => {
    let count = 0;

    if (hasTrailingIcon.value) {
      count += 1;
    }

    if (hasSuffix.value) {
      count += 1;
    }

    if (hasClearControl.value) {
      count += 1;
    }

    return count;
  });

  const rightPadding = computed(() => {
    if (rightDecorators.value === 3) {
      return "pr-[5.5rem]";
    }

    if (rightDecorators.value === 2) {
      return "pr-16";
    }

    if (rightDecorators.value === 1) {
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

  const trailingOffset = computed(() => {
    if (hasSuffix.value && hasClearControl.value) {
      return "right-20";
    }

    if (hasSuffix.value || hasClearControl.value) {
      return "right-12";
    }

    return "right-4";
  });

  const suffixOffset = computed(() => {
    if (hasTrailingIcon.value && hasClearControl.value) {
      return "right-20";
    }

    if (hasTrailingIcon.value || hasClearControl.value) {
      return "right-12";
    }

    return "right-4";
  });

  const clearOffset = computed(() => {
    if (hasTrailingIcon.value && hasSuffix.value) {
      return "right-20";
    }

    if (hasTrailingIcon.value || hasSuffix.value) {
      return "right-12";
    }

    return "right-3";
  });

  const baseFieldClasses = computed(() =>
    cn(
      inputVariants({
        variant: props.variant,
        state: fieldState.value,
        multiline: isMultiline.value,
        label: hasLabel.value,
      }),
      leftPadding.value,
      rightPadding.value,
      !props.resize && isMultiline.value ? "resize-none" : "resize-y",
      props.class,
    ),
  );

  const labelClasses = computed(() =>
    cn(
      inputLabelVariants({
        variant: props.variant,
        state: fieldState.value,
      }),
      labelOffset.value,
    ),
  );

  const assistiveTextClasses = computed(() =>
    cn(
      inputAssistiveTextVariants({
        state: fieldState.value,
      }),
    ),
  );

  const activeLineClasses = computed(() => {
    if (fieldState.value === "error") {
      return "bg-[var(--error)]";
    }

    if (fieldState.value === "destructive") {
      return "bg-destructive";
    }

    if (fieldState.value === "info") {
      return "bg-info";
    }

    if (fieldState.value === "warning") {
      return "bg-warning";
    }

    if (fieldState.value === "success") {
      return "bg-success";
    }

    return "bg-[var(--field-focus)]";
  });

  const showBottomLine = computed(() => props.variant === "filled");

  function withTrim(value: string): string {
    if (!props.modelModifiers.trim) {
      return value;
    }

    return value.trim();
  }

  function withMaxLength(value: string): string {
    if (typeof props.maxLength !== "number") {
      return value;
    }

    return value.slice(0, props.maxLength);
  }

  function normalizeNumberValue(value: string): string {
    let normalizedValue = value;

    const minusIndex = normalizedValue.indexOf("-");
    const dotIndex = normalizedValue.indexOf(".");

    if (minusIndex > -1) {
      normalizedValue = minusIndex === 0 ? `-${normalizedValue.replace(/-/g, "")}` : normalizedValue.replace(/-/g, "");
    }

    if (dotIndex > -1) {
      normalizedValue = `${normalizedValue.slice(0, dotIndex + 1)}${normalizedValue.slice(dotIndex).replace(/\./g, "")}`;
    }

    return normalizedValue.replace(/[^-0-9.]/g, "");
  }

  function normalizeValue(value: string): string {
    let nextValue = value;

    if (props.type === "number") {
      nextValue = normalizeNumberValue(nextValue);
    }

    return withMaxLength(nextValue);
  }

  function focusField(): void {
    fieldRef.value?.focus();
  }

  function blurField(): void {
    fieldRef.value?.blur();
  }

  function selectField(): void {
    if (!fieldRef.value || isMultiline.value) {
      return;
    }

    (fieldRef.value as HTMLInputElement).select();
  }

  function handleMousedown(event: MouseEvent): void {
    if (props.disabled || props.readonly || event.target === fieldRef.value) {
      return;
    }

    focusField();
    event.preventDefault();
  }

  function handleFocus(event: FocusEvent): void {
    emits("focus", event);
  }

  function handleBlur(event: FocusEvent): void {
    if (props.modelModifiers.trim) {
      const trimmedValue = withTrim(String(modelValue.value ?? ""));
      modelValue.value = trimmedValue;
    }

    emits("blur", event);
  }

  function handleCompositionStart(): void {
    isComposing.value = true;
  }

  function handleCompositionEnd(event: Event): void {
    if (!isComposing.value) {
      return;
    }

    isComposing.value = false;
    handleInput(event);
  }

  function handleInput(event: Event): void {
    if (isComposing.value) {
      return;
    }

    const target = event.target as HTMLInputElement | HTMLTextAreaElement;
    const nextValue = normalizeValue(target.value);

    if (target.value !== nextValue) {
      target.value = nextValue;
    }

    modelValue.value = nextValue;
    emits("input", { value: nextValue, event });
  }

  function handleChange(event: Event): void {
    const target = event.target as HTMLInputElement | HTMLTextAreaElement;
    const nextValue = withTrim(normalizeValue(target.value));

    if (target.value !== nextValue) {
      target.value = nextValue;
    }

    modelValue.value = nextValue;
    emits("change", { value: nextValue, event });
  }

  function clearValue(): void {
    if (!hasClearControl.value) {
      return;
    }

    modelValue.value = "";
    emits("clear", "");
    focusField();
  }

  defineExpose({
    focus: focusField,
    blur: blurField,
    select: selectField,
    clear: clearValue,
  });
</script>

<template>
  <div class="w-full">
    <div class="group relative" @mousedown="handleMousedown">
      <span v-if="hasLeadingIcon" class="absolute left-4 top-1/2 z-10 flex -translate-y-1/2 items-center text-(--field-label) transition-colors duration-150 peer-focus:text-(--field-focus) peer-disabled:opacity-60">
        <slot name="leading-icon">
          <slot name="prepend-icon">
            <component v-if="leadingIcon && typeof leadingIcon !== 'string'" :is="leadingIcon" class="size-5" />
            <span v-else-if="leadingIcon" class="text-sm font-medium">{{ leadingIcon }}</span>
          </slot>
        </slot>
      </span>

      <span v-if="hasPrefix" :class="cn('absolute top-1/2 z-10 flex -translate-y-1/2 items-center text-sm text-(--field-label) peer-disabled:opacity-60', prefixOffset)">
        <slot name="prefix">
          {{ prefixText }}
        </slot>
      </span>

      <input v-if="normalizedType === 'password' && preventAutoFill && !isMultiline" tabindex="-1" autocomplete="new-password" class="h-0 w-0 border-0 p-0 text-[0] opacity-0" aria-hidden="true" />

      <component
        :is="isMultiline ? 'textarea' : 'input'"
        ref="fieldRef"
        :value="modelValue ?? ''"
        data-slot="input"
        :id="inputId"
        :type="isMultiline ? undefined : normalizedType"
        :rows="isMultiline ? rows : undefined"
        :cols="isMultiline ? cols : undefined"
        :placeholder="placeholderValue"
        :maxlength="maxLength"
        :minlength="minLength"
        :required="required"
        :disabled="disabled"
        :readonly="readonly"
        :autofocus="autofocus"
        :inputmode="type === 'number' ? 'decimal' : undefined"
        :aria-invalid="hasError ? 'true' : undefined"
        :class="baseFieldClasses"
        v-bind="attrs"
        @focus="handleFocus"
        @blur="handleBlur"
        @input="handleInput"
        @change="handleChange"
        @compositionstart="handleCompositionStart"
        @compositionend="handleCompositionEnd"
      />

      <span v-if="hasTrailingIcon" :class="cn('absolute top-1/2 z-10 flex -translate-y-1/2 items-center text-(--field-label) transition-colors duration-150 peer-focus:text-(--field-focus) peer-disabled:opacity-60', trailingOffset)">
        <slot name="trailing-icon">
          <slot name="append-icon">
            <component v-if="trailingIcon && typeof trailingIcon !== 'string'" :is="trailingIcon" class="size-5" />
            <span v-else-if="trailingIcon" class="text-sm font-medium">{{ trailingIcon }}</span>
          </slot>
        </slot>
      </span>

      <span v-if="hasSuffix" :class="cn('absolute top-1/2 z-10 flex -translate-y-1/2 items-center text-sm text-(--field-label) peer-disabled:opacity-60', suffixOffset)">
        <slot name="suffix">
          {{ suffixText }}
        </slot>
      </span>

      <button
        v-if="hasClearControl"
        type="button"
        :class="
          cn('absolute top-1/2 z-20 flex size-6 -translate-y-1/2 items-center justify-center rounded-full text-(--field-label) transition-colors duration-150 hover:text-(--field-focus) focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-(--field-focus)', clearOffset)
        "
        :disabled="disabled || readonly"
        aria-label="Clear input"
        @click.stop="clearValue"
      >
        <slot name="clear-icon" :clear="clearValue">
          <component :is="clearIcon ?? CircleX" class="size-4" />
        </slot>
      </button>

      <label v-if="label" :for="inputId" :class="labelClasses">
        {{ label }}
        <span v-if="showAsterisk" class="text-(--error)"> *</span>
      </label>

      <div v-if="showBottomLine" class="pointer-events-none absolute inset-x-0 bottom-0 h-0.5 overflow-hidden rounded-full bg-(--outline)/70">
        <span :class="cn('block h-full w-full origin-center scale-x-0 transition-transform duration-200 group-focus-within:scale-x-100', activeLineClasses, hasError && 'scale-x-100')" />
      </div>
    </div>

    <div v-if="supportingText || showCounter" class="mt-2 flex items-start justify-between gap-3">
      <p v-if="supportingText" :class="assistiveTextClasses">
        {{ supportingText }}
      </p>
      <p v-if="showCounter" :class="assistiveTextClasses">{{ currentLength }} / {{ maxLength }}</p>
    </div>
  </div>
</template>
