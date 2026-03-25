<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import { cn } from 'tailwind-variants';
import { dropdownMenuShortcutVariants } from './variants';

const props = defineProps<{
  class?: HTMLAttributes['class'];
}>();
</script>

<template>
  <span data-slot="dropdown-menu-shortcut" :class="cn(dropdownMenuShortcutVariants(), props.class)">
    <slot />
  </span>
</template>
