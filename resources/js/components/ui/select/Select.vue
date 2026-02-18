<script setup lang="ts">
  import type { Component, HTMLAttributes } from "vue";
  import { computed, ref, useId, watch } from "vue";
  import { useVModel } from "@vueuse/core";
  import { ChevronDown, CircleX } from "lucide-vue-next";
  import type { InputVariants } from "@/components/ui/input";
  import { inputAssistiveTextVariants, inputVariants } from "@/components/ui/input";
  import { DropdownMenu, DropdownMenuContent, DropdownMenuRadioGroup, DropdownMenuRadioItem, DropdownMenuTrigger } from "@/components/ui/dropdown-menu";
  import { cn } from "@/lib/utils";
  import type { SelectOption, SelectOptionVariant } from ".";
  import { selectLabelVariants, selectOptionVariants } from ".";

  const props = withDefaults(
    defineProps<{
      defaultValue?: string;
      modelValue?: string;
      options: SelectOption[];
      class?: HTMLAttributes["class"];
      triggerClass?: HTMLAttributes["class"];
      label?: string;
      supportingText?: string;
      errorText?: string;
      message?: string;
      variant?: InputVariants["variant"];
      state?: InputVariants["state"];
      error?: boolean | string;
      required?: boolean;
      noAsterisk?: boolean;
      disabled?: boolean;
      readonly?: boolean;
      id?: string;
      name?: string;
      placeholder?: string;
      optionVariant?: SelectOptionVariant;
      optionClass?: HTMLAttributes["class"];
      contentClass?: HTMLAttributes["class"];
      clearable?: boolean;
      leadingIcon?: Component | string;
      trailingIcon?: Component | string;
      clearIcon?: Component;
    }>(),
    {
      variant: "filled",
      state: "default",
      placeholder: "Select an option",
      optionVariant: "primary",
      clearable: false,
    },
  );

  const emit = defineEmits<{
    (event: "update:modelValue", value: string): void;
    (event: "change", value: string): void;
    (event: "clear", value: string): void;
    (event: "focus", eventPayload: FocusEvent): void;
    (event: "blur", eventPayload: FocusEvent): void;
    (event: "openChange", isOpen: boolean): void;
  }>();

  const generatedId = useId();
  const open = ref(false);

  const modelValue = useVModel(props, "modelValue", emit, {
    passive: true,
    defaultValue: props.defaultValue,
  });

  watch(
    () => [props.disabled, props.readonly],
    ([disabled, readonly]) => {
      if (disabled || readonly) {
        open.value = false;
      }
    },
  );

  watch(open, (value) => {
    emit("openChange", value);
  });

  const hasLabel = computed(() => Boolean(props.label));
  const showAsterisk = computed(() => props.required && !props.noAsterisk);
  const fieldId = computed(() => props.id ?? `select-${generatedId}`);
  const menuId = computed(() => `${fieldId.value}-menu`);

  const hasError = computed(() => {
    if (props.state === "error" || props.state === "destructive") {
      return true;
    }

    if (typeof props.error === "string") {
      return true;
    }

    return Boolean(props.error);
  });

  const fieldState = computed<InputVariants["state"]>(() => {
    if (hasError.value) {
      if (props.state === "destructive") {
        return "destructive";
      }

      return "error";
    }

    if (props.state === "info") {
      return "info";
    }

    if (props.state === "warning") {
      return "warning";
    }

    if (props.state === "success") {
      return "success";
    }

    return "default";
  });

  const hasSelectedValue = computed(() => {
    if (modelValue.value === null || modelValue.value === undefined) {
      return false;
    }

    return String(modelValue.value).length > 0;
  });

  const selectedOption = computed(() => {
    if (!hasSelectedValue.value) {
      return undefined;
    }

    return props.options.find((option) => option.value === modelValue.value);
  });

  const hasValue = computed(() => hasSelectedValue.value);

  const selectedLabel = computed(() => {
    if (selectedOption.value) {
      return selectedOption.value.label;
    }

    if (modelValue.value) {
      return modelValue.value;
    }

    if (hasLabel.value) {
      return "";
    }

    return props.placeholder;
  });

  const supportingText = computed(() => {
    if (hasError.value) {
      if (typeof props.error === "string") {
        return props.error;
      }

      return props.errorText ?? props.message ?? props.supportingText;
    }

    return props.supportingText ?? props.message;
  });

  const hasLeadingIcon = computed(() => Boolean(props.leadingIcon));
  const hasTrailingIcon = computed(() => Boolean(props.trailingIcon));
  const hasClearControl = computed(() => props.clearable && hasValue.value && !props.disabled && !props.readonly);

  const leftPadding = computed(() => (hasLeadingIcon.value ? "pl-12" : "pl-4"));

  const rightDecorators = computed(() => {
    let count = 1;

    if (hasTrailingIcon.value) {
      count += 1;
    }

    if (hasClearControl.value) {
      count += 1;
    }

    return count;
  });

  const rightPadding = computed(() => {
    if (rightDecorators.value >= 3) {
      return "pr-20";
    }

    if (rightDecorators.value === 2) {
      return "pr-14";
    }

    return "pr-10";
  });

  const arrowOffset = computed(() => "right-3");
  const trailingOffset = computed(() => (hasClearControl.value ? "right-16" : "right-10"));
  const clearOffset = computed(() => "right-10");

  const triggerClasses = computed(() =>
    cn(
      inputVariants({
        variant: props.variant,
        state: fieldState.value,
        multiline: false,
        label: hasLabel.value,
      }),
      "group relative flex w-full items-center text-left",
      leftPadding.value,
      rightPadding.value,
      !hasValue.value && "text-[var(--field-label)]",
      props.class,
      props.triggerClass,
    ),
  );

  const labelClasses = computed(() =>
    cn(
      selectLabelVariants({
        variant: props.variant,
        state: fieldState.value,
      }),
      "left-4",
    ),
  );

  const assistiveTextClasses = computed(() =>
    cn(
      inputAssistiveTextVariants({
        state: fieldState.value,
      }),
    ),
  );

  const optionStateClasses = computed(() =>
    selectOptionVariants({
      variant: props.optionVariant,
    }),
  );

  const chevronClasses = computed(() => cn("size-4 shrink-0 text-[var(--field-label)] transition-transform duration-150", open.value && "rotate-180", hasError.value && "text-[var(--error)]"));

  function handleSelectionChange(value: string): void {
    modelValue.value = value;
    emit("change", value);

    if (open.value) {
      open.value = false;
    }
  }

  function clearValue(): void {
    if (!hasClearControl.value) {
      return;
    }

    modelValue.value = "";
    emit("clear", "");
  }

  function handleTriggerKeydown(event: KeyboardEvent): void {
    if (props.disabled || props.readonly) {
      return;
    }

    if (event.key === "Enter" || event.key === " ") {
      event.preventDefault();
      open.value = !open.value;
      return;
    }

    if (event.key === "ArrowDown") {
      event.preventDefault();
      open.value = true;
      return;
    }

    if (event.key === "Escape" && open.value) {
      event.preventDefault();
      open.value = false;
    }
  }
</script>

<template>
  <div class="w-full">
    <input v-if="name" type="hidden" :name="name" :value="modelValue ?? ''" />

    <div class="relative">
      <DropdownMenu v-model:open="open" :modal="false">
        <DropdownMenuTrigger as-child>
          <button
            :id="fieldId"
            type="button"
            :disabled="disabled || readonly"
            :data-filled="hasValue ? 'true' : 'false'"
            :data-open="open ? 'true' : 'false'"
            role="combobox"
            :aria-invalid="hasError ? 'true' : undefined"
            :aria-expanded="open ? 'true' : 'false'"
            :aria-controls="menuId"
            :aria-required="required ? 'true' : undefined"
            :aria-readonly="readonly ? 'true' : undefined"
            :class="triggerClasses"
            @keydown="handleTriggerKeydown"
            @focus="(event) => emit('focus', event)"
            @blur="(event) => emit('blur', event)"
          >
            <span v-if="hasLeadingIcon" class="absolute left-4 top-1/2 z-10 flex -translate-y-1/2 items-center text-[var(--field-label)]">
              <slot name="leading-icon">
                <slot name="prepend-icon">
                  <component v-if="leadingIcon && typeof leadingIcon !== 'string'" :is="leadingIcon" class="size-5" />
                  <span v-else-if="leadingIcon" class="text-sm font-medium">{{ leadingIcon }}</span>
                </slot>
              </slot>
            </span>

            <span class="truncate text-left">
              <slot name="selected" :option="selectedOption" :value="modelValue">
                {{ selectedLabel }}
              </slot>
            </span>

            <span
              v-if="hasClearControl"
              :class="
                cn(
                  'absolute top-1/2 z-20 flex size-6 -translate-y-1/2 items-center justify-center rounded-full text-[var(--field-label)] transition-colors duration-150 hover:text-[var(--field-focus)] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[color:var(--field-focus)]',
                  clearOffset,
                )
              "
              role="button"
              tabindex="0"
              aria-label="Clear selection"
              @click.stop="clearValue"
              @keydown.enter.prevent="clearValue"
              @keydown.space.prevent="clearValue"
            >
              <slot name="clear-icon" :clear="clearValue">
                <component :is="clearIcon ?? CircleX" class="size-4" />
              </slot>
            </span>

            <span v-if="hasTrailingIcon" :class="cn('absolute top-1/2 z-10 flex -translate-y-1/2 items-center text-[var(--field-label)]', trailingOffset)">
              <slot name="trailing-icon">
                <slot name="append-icon">
                  <component v-if="trailingIcon && typeof trailingIcon !== 'string'" :is="trailingIcon" class="size-5" />
                  <span v-else-if="trailingIcon" class="text-sm font-medium">{{ trailingIcon }}</span>
                </slot>
              </slot>
            </span>

            <span :class="cn('absolute top-1/2 z-10 -translate-y-1/2', arrowOffset)">
              <slot name="arrow-icon" :open="open">
                <ChevronDown :class="chevronClasses" />
              </slot>
            </span>
          </button>
        </DropdownMenuTrigger>

        <DropdownMenuContent :id="menuId" align="start" :side-offset="4" :class="cn('max-h-70 w-(--reka-dropdown-menu-trigger-width) overflow-y-auto border-border/60 p-1 shadow-(--elevation-2)', contentClass)">
          <DropdownMenuRadioGroup :model-value="modelValue" @update:model-value="handleSelectionChange">
            <DropdownMenuRadioItem v-for="option in options" :key="option.value" :value="option.value" :disabled="option.disabled" :class="cn('min-h-11 cursor-pointer rounded-sm px-3 py-2 pl-3 text-sm font-medium [&>span:first-child]:hidden', optionStateClasses, optionClass)">
              {{ option.label }}
            </DropdownMenuRadioItem>
          </DropdownMenuRadioGroup>
        </DropdownMenuContent>
      </DropdownMenu>

      <label v-if="label" :for="fieldId" :class="labelClasses">
        {{ label }}
        <span v-if="showAsterisk" class="text-[var(--error)]"> *</span>
      </label>
    </div>

    <div v-if="supportingText" class="mt-2">
      <p :class="assistiveTextClasses">
        {{ supportingText }}
      </p>
    </div>
  </div>
</template>
