<script setup lang="ts">
  import type { HTMLAttributes } from "vue";
  import { computed, ref, useId, watch } from "vue";
  import { useVModel } from "@vueuse/core";
  import { CalendarDays, ChevronLeft, ChevronRight, CircleX } from "lucide-vue-next";
  import type { InputVariants } from "@/components/ui/input/variants";
  import { inputAssistiveTextVariants, inputVariants } from "@/components/ui/input/variants";
  import DropdownMenu from "@/components/ui/dropdown-menu/DropdownMenu.vue";
  import DropdownMenuContent from "@/components/ui/dropdown-menu/DropdownMenuContent.vue";
  import DropdownMenuTrigger from "@/components/ui/dropdown-menu/DropdownMenuTrigger.vue";
  import { cn } from "@/lib/utils";
  import { datePickerDayVariants } from "./styles";

  const props = withDefaults(
    defineProps<{
      defaultValue?: string;
      modelValue?: string;
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
      contentClass?: HTMLAttributes["class"];
      clearable?: boolean;
      min?: string;
      max?: string;
      locale?: string;
      weekStartsOn?: 0 | 1;
    }>(),
    {
      variant: "filled",
      state: "default",
      placeholder: "Select a date",
      clearable: false,
      locale: "en-US",
      weekStartsOn: 0,
    },
  );

  const emit = defineEmits<{
    (event: "update:modelValue", value: string): void;
    (event: "change", value: string): void;
    (event: "clear", value: string): void;
    (event: "focus", payload: FocusEvent): void;
    (event: "blur", payload: FocusEvent): void;
    (event: "openChange", isOpen: boolean): void;
  }>();

  const generatedId = useId();
  const open = ref(false);

  const modelValue = useVModel(props, "modelValue", emit, {
    passive: true,
    defaultValue: props.defaultValue,
  });

  const hasLabel = computed(() => Boolean(props.label));
  const showAsterisk = computed(() => props.required && !props.noAsterisk);
  const fieldId = computed(() => props.id ?? `date-picker-${generatedId}`);
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

  const hasValue = computed(() => Boolean(modelValue.value));
  const hasClearControl = computed(() => props.clearable && hasValue.value && !props.disabled && !props.readonly);

  const selectedDate = computed(() => parseIsoDate(modelValue.value));
  const visibleMonth = ref<Date>(selectedDate.value ?? startOfMonth(new Date()));

  watch(
    () => modelValue.value,
    (value) => {
      const parsedDate = parseIsoDate(value);

      if (parsedDate) {
        visibleMonth.value = startOfMonth(parsedDate);
      }
    },
  );

  watch(open, (value) => {
    emit("openChange", value);
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
      "group relative flex w-full items-center justify-between gap-3 px-4 pr-12 text-left",
      !hasValue.value && "text-[var(--field-label)]",
      props.class,
      props.triggerClass,
    ),
  );

  const labelClasses = computed(() =>
    cn(
      "pointer-events-none absolute top-4 left-4 origin-left text-sm text-[var(--field-label)] transition-[transform,color,top] duration-150 peer-focus:top-2 peer-focus:scale-90 peer-data-[open=true]:top-2 peer-data-[open=true]:scale-90 peer-data-[filled=true]:top-2 peer-data-[filled=true]:scale-90 peer-focus:text-[var(--field-focus)] peer-data-[open=true]:text-[var(--field-focus)] peer-disabled:opacity-60",
      fieldState.value === "error" && "text-[var(--error)] peer-focus:text-[var(--error)] peer-data-[open=true]:text-[var(--error)]",
      fieldState.value === "destructive" && "text-destructive peer-focus:text-destructive peer-data-[open=true]:text-destructive",
      fieldState.value === "info" && "text-info peer-focus:text-info peer-data-[open=true]:text-info",
      fieldState.value === "warning" && "text-warning peer-focus:text-warning peer-data-[open=true]:text-warning",
      fieldState.value === "success" && "text-success peer-focus:text-success peer-data-[open=true]:text-success",
    ),
  );

  const assistiveTextClasses = computed(() =>
    cn(
      inputAssistiveTextVariants({
        state: fieldState.value,
      }),
    ),
  );

  const formattedValue = computed(() => {
    const date = selectedDate.value;

    if (!date) {
      return props.placeholder;
    }

    return new Intl.DateTimeFormat(props.locale, {
      year: "numeric",
      month: "short",
      day: "numeric",
    }).format(date);
  });

  const monthLabel = computed(() =>
    new Intl.DateTimeFormat(props.locale, {
      month: "long",
      year: "numeric",
    }).format(visibleMonth.value),
  );

  const weekdayLabels = computed(() => {
    const labels: string[] = [];
    const weekStart = props.weekStartsOn;
    const base = new Date(Date.UTC(2024, 0, 7 + weekStart));

    for (let index = 0; index < 7; index += 1) {
      const date = new Date(base);
      date.setUTCDate(base.getUTCDate() + index);
      labels.push(
        new Intl.DateTimeFormat(props.locale, {
          weekday: "short",
        }).format(date),
      );
    }

    return labels;
  });

  const dayCells = computed(() => buildCalendarGrid(visibleMonth.value, props.weekStartsOn));

  function parseIsoDate(value?: string): Date | null {
    if (!value) {
      return null;
    }

    const parts = value.split("-").map((part) => Number(part));

    if (parts.length !== 3 || Number.isNaN(parts[0]) || Number.isNaN(parts[1]) || Number.isNaN(parts[2])) {
      return null;
    }

    const [year, month, day] = parts;
    const date = new Date(year, month - 1, day);

    if (date.getFullYear() !== year || date.getMonth() !== month - 1 || date.getDate() !== day) {
      return null;
    }

    return date;
  }

  function startOfMonth(date: Date): Date {
    return new Date(date.getFullYear(), date.getMonth(), 1);
  }

  function toIsoDate(date: Date): string {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, "0");
    const day = String(date.getDate()).padStart(2, "0");

    return `${year}-${month}-${day}`;
  }

  function buildCalendarGrid(date: Date, weekStartsOn: 0 | 1): Date[] {
    const monthStart = new Date(date.getFullYear(), date.getMonth(), 1);
    const dayOffset = (monthStart.getDay() - weekStartsOn + 7) % 7;
    const gridStart = new Date(monthStart);
    gridStart.setDate(monthStart.getDate() - dayOffset);

    const cells: Date[] = [];

    for (let index = 0; index < 42; index += 1) {
      const nextDate = new Date(gridStart);
      nextDate.setDate(gridStart.getDate() + index);
      cells.push(nextDate);
    }

    return cells;
  }

  function isSameDay(left: Date | null, right: Date): boolean {
    if (!left) {
      return false;
    }

    return left.getFullYear() === right.getFullYear() && left.getMonth() === right.getMonth() && left.getDate() === right.getDate();
  }

  function isInCurrentMonth(date: Date): boolean {
    return date.getMonth() === visibleMonth.value.getMonth() && date.getFullYear() === visibleMonth.value.getFullYear();
  }

  function isBeforeMin(date: Date): boolean {
    const minDate = parseIsoDate(props.min);

    if (!minDate) {
      return false;
    }

    return toIsoDate(date) < toIsoDate(minDate);
  }

  function isAfterMax(date: Date): boolean {
    const maxDate = parseIsoDate(props.max);

    if (!maxDate) {
      return false;
    }

    return toIsoDate(date) > toIsoDate(maxDate);
  }

  function isDisabledDay(date: Date): boolean {
    if (props.disabled || props.readonly) {
      return true;
    }

    return isBeforeMin(date) || isAfterMax(date);
  }

  function goToPreviousMonth(): void {
    const nextMonth = new Date(visibleMonth.value);
    nextMonth.setMonth(nextMonth.getMonth() - 1);
    visibleMonth.value = startOfMonth(nextMonth);
  }

  function goToNextMonth(): void {
    const nextMonth = new Date(visibleMonth.value);
    nextMonth.setMonth(nextMonth.getMonth() + 1);
    visibleMonth.value = startOfMonth(nextMonth);
  }

  function selectDate(date: Date): void {
    if (isDisabledDay(date)) {
      return;
    }

    const nextValue = toIsoDate(date);
    modelValue.value = nextValue;
    emit("change", nextValue);
    open.value = false;
  }

  function clearValue(): void {
    if (!hasClearControl.value) {
      return;
    }

    modelValue.value = "";
    emit("clear", "");
  }

  function selectToday(): void {
    const today = new Date();

    if (isDisabledDay(today)) {
      return;
    }

    selectDate(today);
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
            <span class="truncate text-left">{{ formattedValue }}</span>

            <span
              v-if="hasClearControl"
              class="absolute top-1/2 right-11 z-20 flex size-6 -translate-y-1/2 items-center justify-center rounded-full text-[var(--field-label)] transition-colors duration-150 hover:text-[var(--field-focus)]"
              role="button"
              tabindex="0"
              aria-label="Clear date"
              @click.stop="clearValue"
              @keydown.enter.prevent="clearValue"
              @keydown.space.prevent="clearValue"
            >
              <CircleX class="size-4" />
            </span>

            <span class="absolute top-1/2 right-3 z-10 -translate-y-1/2 text-[var(--field-label)]">
              <CalendarDays class="size-4" />
            </span>
          </button>
        </DropdownMenuTrigger>

        <DropdownMenuContent :id="menuId" align="start" :side-offset="4" :class="cn('w-82 border-border/60 p-3 shadow-(--elevation-2)', contentClass)">
          <div class="flex items-center justify-between">
            <button type="button" class="inline-flex size-8 items-center justify-center rounded-md text-muted-foreground transition-colors hover:bg-muted hover:text-foreground" :disabled="disabled || readonly" @click="goToPreviousMonth">
              <ChevronLeft class="size-4" />
            </button>

            <p class="text-sm font-semibold">{{ monthLabel }}</p>

            <button type="button" class="inline-flex size-8 items-center justify-center rounded-md text-muted-foreground transition-colors hover:bg-muted hover:text-foreground" :disabled="disabled || readonly" @click="goToNextMonth">
              <ChevronRight class="size-4" />
            </button>
          </div>

          <div class="mt-3 grid grid-cols-7 gap-1">
            <p v-for="weekday in weekdayLabels" :key="weekday" class="text-muted-foreground flex h-8 items-center justify-center text-xs font-medium">
              {{ weekday }}
            </p>

            <button
              v-for="date in dayCells"
              :key="`${date.getFullYear()}-${date.getMonth()}-${date.getDate()}`"
              type="button"
              :disabled="isDisabledDay(date)"
              :class="
                cn(
                  datePickerDayVariants({
                    variant: isSameDay(selectedDate, date) ? 'selected' : isSameDay(new Date(), date) ? 'today' : isInCurrentMonth(date) ? 'default' : 'muted',
                  }),
                )
              "
              @click="selectDate(date)"
            >
              {{ date.getDate() }}
            </button>
          </div>

          <div class="mt-3 flex items-center justify-between gap-2 border-t border-border/70 pt-3">
            <button type="button" class="text-muted-foreground hover:text-foreground inline-flex items-center rounded-md px-2 py-1 text-xs font-medium transition-colors" :disabled="disabled || readonly" @click="clearValue">Clear</button>

            <button type="button" class="inline-flex items-center rounded-md bg-primary px-2 py-1 text-xs font-medium text-primary-foreground transition-colors hover:bg-primary/90 disabled:opacity-45" :disabled="disabled || readonly" @click="selectToday">Today</button>
          </div>
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
