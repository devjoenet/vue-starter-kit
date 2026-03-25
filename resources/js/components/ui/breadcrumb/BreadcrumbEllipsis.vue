<script lang="ts" setup>
import type { HTMLAttributes } from 'vue';
import { MoreHorizontal } from 'lucide-vue-next';
import { cn } from 'tailwind-variants';
import { breadcrumbEllipsisVariants } from './variants';

const props = defineProps<{
  class?: HTMLAttributes['class'];
}>();
</script>

<template>
  <span data-slot="breadcrumb-ellipsis" role="presentation" aria-hidden="true" :class="cn(breadcrumbEllipsisVariants(), props.class)">
    <slot>
      <MoreHorizontal class="size-4" />
    </slot>
    <span class="sr-only">More</span>
  </span>
</template>
