<script setup lang="ts">
  import type { HTMLAttributes } from "vue";
  import { ChevronDown } from "lucide-vue-next";
  import { computed } from "vue";
  import type { ButtonVariants } from "@/components/ui/button";
  import { Button } from "@/components/ui/button";
  import { DropdownMenu, DropdownMenuContent, DropdownMenuRadioGroup, DropdownMenuRadioItem, DropdownMenuTrigger } from "@/components/ui/dropdown-menu";
  import { cn } from "@/lib/utils";

  type PermissionGroupOption = {
    value: string;
    label: string;
  };

  type PermissionGroupOptionVariant = NonNullable<ButtonVariants["variant"]>;

  const groupOptions: PermissionGroupOption[] = [
    { value: "users", label: "Users" },
    { value: "roles", label: "Roles" },
    { value: "permissions", label: "Permissions" },
  ];

  const optionVariantClasses: Record<PermissionGroupOptionVariant, string> = {
    muted: "hover:bg-muted/80 hover:text-foreground focus:bg-muted/80 focus:text-foreground",
    primary: "hover:bg-primary/12 hover:text-primary focus:bg-primary/12 focus:text-primary",
    secondary: "hover:bg-secondary/12 hover:text-secondary focus:bg-secondary/12 focus:text-secondary",
    info: "hover:bg-info/12 hover:text-info focus:bg-info/12 focus:text-info",
    warning: "hover:bg-warning/12 hover:text-warning focus:bg-warning/12 focus:text-warning",
    success: "hover:bg-success/12 hover:text-success focus:bg-success/12 focus:text-success",
    destructive: "hover:bg-destructive/12 hover:text-destructive focus:bg-destructive/12 focus:text-destructive",
  };

  const props = withDefaults(
    defineProps<{
      modelValue: string;
      disabled?: boolean;
      error?: string;
      label?: string;
      id?: string;
      triggerAppearance?: ButtonVariants["appearance"];
      optionVariant?: PermissionGroupOptionVariant;
      optionClass?: HTMLAttributes["class"];
      contentClass?: HTMLAttributes["class"];
    }>(),
    {
      label: "Group",
      id: "permission-group",
      triggerAppearance: "tonal",
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

  const selectedOptionLabel = computed(() => {
    const selectedOption = groupOptions.find((option) => option.value === modelValue.value);

    if (selectedOption) {
      return selectedOption.label;
    }

    return modelValue.value;
  });

  const optionStateClasses = computed(() => optionVariantClasses[props.optionVariant]);
</script>

<template>
  <div class="space-y-1">
    <div class="text-sm font-medium opacity-80">{{ label }}</div>

    <DropdownMenu>
      <DropdownMenuTrigger as-child>
        <Button
          :id="id"
          type="button"
          :disabled="disabled"
          :appearance="triggerAppearance"
          variant="muted"
          rounded="sm"
          :aria-invalid="error ? 'true' : undefined"
          :class="
            cn(
              'h-14 w-full justify-between border px-4 text-sm font-normal outline-none transition-[border-color,box-shadow]',
              error ? 'border-destructive text-destructive focus-visible:border-destructive focus-visible:ring-destructive' : 'border-[color:var(--outline)]',
            )
          "
        >
          <span class="truncate text-left">{{ selectedOptionLabel }}</span>
          <ChevronDown class="size-4 shrink-0 text-muted-foreground/90" />
        </Button>
      </DropdownMenuTrigger>

      <DropdownMenuContent align="start" :side-offset="4" :class="cn('w-(--reka-dropdown-menu-trigger-width) border-border/60 shadow-(--elevation-2)', contentClass)">
        <DropdownMenuRadioGroup v-model="modelValue">
          <DropdownMenuRadioItem
            v-for="option in groupOptions"
            :key="option.value"
            :value="option.value"
            :class="cn('cursor-pointer rounded-md px-3 py-2 pl-3 text-sm font-medium [&>span:first-child]:hidden', optionStateClasses, optionClass)"
          >
            {{ option.label }}
          </DropdownMenuRadioItem>
        </DropdownMenuRadioGroup>
      </DropdownMenuContent>
    </DropdownMenu>

    <p v-if="error" class="text-xs text-destructive">
      {{ error }}
    </p>
  </div>
</template>
