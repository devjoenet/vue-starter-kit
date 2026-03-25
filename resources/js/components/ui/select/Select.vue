<script setup lang="ts">
import { useVModel } from '@vueuse/core';
import { ChevronDown } from 'lucide-vue-next';
import type { Component, HTMLAttributes } from 'vue';
import { computed, ref, useId, useSlots } from 'vue';
import DropdownMenu from '@/components/ui/dropdown-menu/DropdownMenu.vue';
import DropdownMenuContent from '@/components/ui/dropdown-menu/DropdownMenuContent.vue';
import DropdownMenuRadioGroup from '@/components/ui/dropdown-menu/DropdownMenuRadioGroup.vue';
import DropdownMenuRadioItem from '@/components/ui/dropdown-menu/DropdownMenuRadioItem.vue';
import DropdownMenuTrigger from '@/components/ui/dropdown-menu/DropdownMenuTrigger.vue';
import type { InputVariants } from '@/components/ui/input/variants';
import { inputAssistiveTextVariants, inputVariants } from '@/components/ui/input/variants';
import { cn } from 'tailwind-variants';
import FieldAdornmentIcon from '../form-field/FieldAdornmentIcon.vue';
import FieldAssistiveText from '../form-field/FieldAssistiveText.vue';
import FieldClearControl from '../form-field/FieldClearControl.vue';
import FieldHiddenInput from '../form-field/FieldHiddenInput.vue';
import FieldLabel from '../form-field/FieldLabel.vue';
import { useDisclosureTrigger } from '../form-field/useDisclosureTrigger';
import { useFieldState } from '../form-field/useFieldState';
import type { SelectOption, SelectOptionVariant } from './variants';
import { selectLabelVariants, selectOptionVariants } from './variants';

const props = withDefaults(
  defineProps<{
    defaultValue?: string;
    modelValue?: string;
    options: SelectOption[];
    class?: HTMLAttributes['class'];
    triggerClass?: HTMLAttributes['class'];
    label?: string;
    supportingText?: string;
    errorText?: string;
    message?: string;
    variant?: InputVariants['variant'];
    state?: InputVariants['state'];
    error?: boolean | string;
    required?: boolean;
    noAsterisk?: boolean;
    disabled?: boolean;
    readonly?: boolean;
    id?: string;
    name?: string;
    placeholder?: string;
    optionVariant?: SelectOptionVariant;
    optionClass?: HTMLAttributes['class'];
    contentClass?: HTMLAttributes['class'];
    clearable?: boolean;
    leadingIcon?: Component | string;
    trailingIcon?: Component | string;
    clearIcon?: Component;
  }>(),
  {
    variant: 'filled',
    state: 'default',
    placeholder: 'Select an option',
    optionVariant: 'primary',
    clearable: false,
  },
);

const emit = defineEmits<{
  (event: 'update:modelValue', value: string): void;
  (event: 'change', value: string): void;
  (event: 'clear', value: string): void;
  (event: 'focus', eventPayload: FocusEvent): void;
  (event: 'blur', eventPayload: FocusEvent): void;
  (event: 'openChange', isOpen: boolean): void;
}>();

const generatedId = useId();
const open = ref(false);
const slots = useSlots();

const modelValue = useVModel(props, 'modelValue', emit, {
  passive: true,
  defaultValue: props.defaultValue,
});

const hasLabel = computed(() => Boolean(props.label));
const hasClearIconSlot = computed(() => Boolean(slots['clear-icon']));
const fieldId = computed(() => props.id ?? `select-${generatedId}`);
const menuId = computed(() => `${fieldId.value}-menu`);
const { hasError, fieldState, supportingText, showAsterisk } = useFieldState(props);
const assistiveTextId = computed(() => (supportingText.value ? `${fieldId.value}-assistive` : undefined));
const describedBy = computed(() => assistiveTextId.value ?? undefined);

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
    return '';
  }

  return props.placeholder;
});

const hasLeadingIcon = computed(() => Boolean(props.leadingIcon));
const hasTrailingIcon = computed(() => Boolean(props.trailingIcon));
const hasClearControl = computed(() => props.clearable && hasValue.value && !props.disabled && !props.readonly);

const leftPadding = computed(() => (hasLeadingIcon.value ? 'pl-12' : 'pl-4'));

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
    return 'pr-20';
  }

  if (rightDecorators.value === 2) {
    return 'pr-14';
  }

  return 'pr-10';
});

const arrowOffset = computed(() => 'right-3');
const trailingOffset = computed(() => (hasClearControl.value ? 'right-16' : 'right-10'));
const clearOffset = computed(() => 'right-10');

const triggerClasses = computed(() =>
  cn(
    inputVariants({
      variant: props.variant,
      state: fieldState.value,
      multiline: false,
      label: hasLabel.value,
    }),
    'group relative flex w-full items-center text-left',
    leftPadding.value,
    rightPadding.value,
    !hasValue.value && 'text-muted-foreground',
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
    'left-4',
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

const chevronClasses = computed(() => cn('size-4 shrink-0 text-muted-foreground transition-transform duration-150', open.value && 'rotate-180', hasError.value && 'text-destructive'));

const { handleTriggerKeydown } = useDisclosureTrigger({
  open,
  disabled: () => Boolean(props.disabled),
  readonly: () => Boolean(props.readonly),
  closeWhenDisabledOrReadonly: true,
  onOpenChange: (value) => emit('openChange', value),
});

function handleSelectionChange(value: unknown): void {
  if (value === null) {
    modelValue.value = '';
    emit('change', '');
    return;
  }

  const normalizedValue = String(value);

  modelValue.value = normalizedValue;
  emit('change', normalizedValue);

  if (open.value) {
    open.value = false;
  }
}

function clearValue(): void {
  if (!hasClearControl.value) {
    return;
  }

  modelValue.value = '';
  emit('clear', '');
}
</script>

<template>
  <div class="w-full">
    <FieldHiddenInput :name="name" :value="modelValue" />

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
            :aria-describedby="describedBy"
            :aria-required="required ? 'true' : undefined"
            :aria-readonly="readonly ? 'true' : undefined"
            :class="triggerClasses"
            @keydown="handleTriggerKeydown"
            @focus="(event) => emit('focus', event)"
            @blur="(event) => emit('blur', event)"
          >
            <FieldAdornmentIcon v-if="hasLeadingIcon" class="absolute top-1/2 left-4 z-10 flex -translate-y-1/2 items-center text-muted-foreground">
              <slot name="leading-icon">
                <slot name="prepend-icon">
                  <component v-if="leadingIcon && typeof leadingIcon !== 'string'" :is="leadingIcon" class="size-5" />
                  <span v-else-if="leadingIcon" class="text-sm font-medium">{{ leadingIcon }}</span>
                </slot>
              </slot>
            </FieldAdornmentIcon>

            <span class="truncate text-left">
              <slot name="selected" :option="selectedOption" :value="modelValue">
                {{ selectedLabel }}
              </slot>
            </span>

            <FieldClearControl
              v-if="hasClearControl"
              as="span"
              :class="
                cn(
                  'absolute top-1/2 z-20 flex size-6 -translate-y-1/2 items-center justify-center rounded-full text-muted-foreground transition-colors duration-150 hover:text-[var(--ring)] focus-visible:ring-2 focus-visible:ring-[color:var(--ring)] focus-visible:outline-none',
                  clearOffset,
                )
              "
              :disabled="disabled || readonly"
              ariaLabel="Clear selection"
              :icon="clearIcon"
              @clear="clearValue"
            >
              <template v-if="hasClearIconSlot">
                <slot name="clear-icon" :clear="clearValue" />
              </template>
            </FieldClearControl>

            <FieldAdornmentIcon v-if="hasTrailingIcon" :class="cn('absolute top-1/2 z-10 flex -translate-y-1/2 items-center text-muted-foreground', trailingOffset)">
              <slot name="trailing-icon">
                <slot name="append-icon">
                  <component v-if="trailingIcon && typeof trailingIcon !== 'string'" :is="trailingIcon" class="size-5" />
                  <span v-else-if="trailingIcon" class="text-sm font-medium">{{ trailingIcon }}</span>
                </slot>
              </slot>
            </FieldAdornmentIcon>

            <span :class="cn('absolute top-1/2 z-10 -translate-y-1/2', arrowOffset)">
              <slot name="arrow-icon" :open="open">
                <ChevronDown :class="chevronClasses" />
              </slot>
            </span>
          </button>
        </DropdownMenuTrigger>

        <DropdownMenuContent :id="menuId" align="start" :side-offset="4" :class="cn('max-h-70 w-(--reka-dropdown-menu-trigger-width) overflow-y-auto border-border/60 p-1 shadow-(--elevation-2)', contentClass)">
          <DropdownMenuRadioGroup :model-value="modelValue" @update:model-value="handleSelectionChange">
            <DropdownMenuRadioItem
              v-for="option in options"
              :key="option.value"
              :value="option.value"
              :disabled="option.disabled"
              :class="cn('min-h-11 cursor-pointer rounded-sm px-3 py-2 pl-3 text-sm font-medium [&>span:first-child]:hidden', optionStateClasses, optionClass)"
            >
              {{ option.label }}
            </DropdownMenuRadioItem>
          </DropdownMenuRadioGroup>
        </DropdownMenuContent>
      </DropdownMenu>

      <FieldLabel :label="label" :for-id="fieldId" :class="labelClasses" :show-asterisk="showAsterisk" />
    </div>

    <div v-if="supportingText" class="mt-2">
      <FieldAssistiveText :id="assistiveTextId" :text="supportingText" :class="assistiveTextClasses" />
    </div>
  </div>
</template>
