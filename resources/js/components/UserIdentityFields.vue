<script setup lang="ts">
import Input from '@/components/ui/input/Input.vue';

const props = withDefaults(
  defineProps<{
    name?: string;
    email?: string;
    nameDefaultValue?: string;
    emailDefaultValue?: string;
    disabled?: boolean;
    nameError?: string;
    emailError?: string;
    nameId?: string;
    emailId?: string;
    nameLabel?: string;
    emailLabel?: string;
    nameAutocomplete?: string;
    emailAutocomplete?: string;
    nameRequired?: boolean;
    emailRequired?: boolean;
  }>(),
  {
    nameId: 'user-name',
    emailId: 'user-email',
    nameLabel: 'Name',
    emailLabel: 'Email',
    nameAutocomplete: 'name',
    emailAutocomplete: 'username',
    nameRequired: false,
    emailRequired: false,
  },
);

const emit = defineEmits<{
  (event: 'update:name', value: string): void;
  (event: 'update:email', value: string): void;
}>();
</script>

<template>
  <Input
    :id="nameId"
    :model-value="name"
    :default-value="nameDefaultValue"
    name="name"
    :label="nameLabel"
    variant="outlined"
    :disabled="disabled"
    :state="nameError ? 'error' : 'default'"
    :message="nameError"
    :required="nameRequired"
    :autocomplete="nameAutocomplete"
    @update:model-value="(value) => emit('update:name', String(value))"
  />

  <Input
    :id="emailId"
    :model-value="email"
    :default-value="emailDefaultValue"
    type="email"
    name="email"
    :label="emailLabel"
    variant="outlined"
    :disabled="disabled"
    :state="emailError ? 'error' : 'default'"
    :message="emailError"
    :required="emailRequired"
    :autocomplete="emailAutocomplete"
    @update:model-value="(value) => emit('update:email', String(value))"
  />
</template>
