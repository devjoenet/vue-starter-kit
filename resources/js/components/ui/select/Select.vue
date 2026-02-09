<script setup lang="ts">
  import type { HTMLAttributes } from "vue";
  import { computed, ref, useId, watch } from "vue";
  import { useVModel } from "@vueuse/core";
  import { ChevronDown } from "lucide-vue-next";
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
    }>(),
    {
      variant: "filled",
      state: "default",
      placeholder: "Select an option",
      optionVariant: "primary",
    },
  );

  const emit = defineEmits<{
    (event: "update:modelValue", value: string): void;
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

  const selectedOption = computed(() => props.options.find((option) => option.value === modelValue.value));
  const hasValue = computed(() => Boolean(selectedOption.value) || Boolean(modelValue.value));

  const selectedLabel = computed(() => {
    if (selectedOption.value) {
      return selectedOption.value.label;
    }

    if (modelValue.value) {
      return modelValue.value;
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

  const triggerClasses = computed(() =>
    cn(
      inputVariants({
        variant: props.variant,
        state: fieldState.value,
        multiline: false,
        label: hasLabel.value,
      }),
      "flex items-center justify-between gap-3 text-left",
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
          >
            <span class="truncate text-left">{{ selectedLabel }}</span>
            <ChevronDown :class="chevronClasses" />
          </button>
        </DropdownMenuTrigger>

        <DropdownMenuContent :id="menuId" align="start" :side-offset="4" :class="cn('max-h-70 w-(--reka-dropdown-menu-trigger-width) border-border/60 p-1 shadow-(--elevation-2)', contentClass)">
          <DropdownMenuRadioGroup v-model="modelValue">
            <DropdownMenuRadioItem v-for="option in options" :key="option.value" :value="option.value" :disabled="option.disabled" :class="cn('min-h-11 cursor-pointer rounded-sm px-3 py-2 pl-3 text-sm font-medium [&>span:first-child]:hidden', optionStateClasses, optionClass)">
              {{ option.label }}
            </DropdownMenuRadioItem>
          </DropdownMenuRadioGroup>
        </DropdownMenuContent>
      </DropdownMenu>

      <label v-if="label" :for="fieldId" :class="labelClasses">
        {{ label }}
        <span v-if="showAsterisk" class="text-(--error)"> *</span>
      </label>
    </div>

    <div v-if="supportingText" class="mt-2">
      <p :class="assistiveTextClasses">
        {{ supportingText }}
      </p>
    </div>
  </div>
</template>
