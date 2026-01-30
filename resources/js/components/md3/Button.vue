<script setup lang="ts">
  withDefaults(
    defineProps<{
      as?: "button" | "a";
      variant?: "filled" | "tonal" | "outlined" | "text";
      disabled?: boolean;
      type?: "button" | "submit";
    }>(),
    {
      as: "button",
      variant: "filled",
      disabled: false,
      type: "button",
    },
  );
</script>

<template>
  <component :is="as" v-ripple :type="as === 'button' ? type : undefined" :disabled="as === 'button' ? disabled : undefined" :class="['md3-btn', `md3-btn--${variant}`, disabled ? 'opacity-60 pointer-events-none' : '']">
    <slot />
  </component>
</template>
