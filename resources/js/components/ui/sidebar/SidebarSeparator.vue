<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import { cn } from 'tailwind-variants';
import Separator from '@/components/ui/separator/Separator.vue';
import { sidebarSeparatorVariants } from './variants';

const props = defineProps<{
  class?: HTMLAttributes['class'];
}>();
</script>

<template>
  <Separator data-slot="sidebar-separator" data-sidebar="separator" :class="cn(sidebarSeparatorVariants(), props.class)">
    <slot />
  </Separator>
</template>
