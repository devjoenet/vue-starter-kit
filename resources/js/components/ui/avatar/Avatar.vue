<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import { AvatarRoot } from 'reka-ui';
import { cn } from 'tailwind-variants';
import { avatarVariants } from './variants';

const props = defineProps<{
  class?: HTMLAttributes['class'];
}>();
</script>

<template>
  <AvatarRoot data-slot="avatar" :class="cn(avatarVariants(), props.class)">
    <slot />
  </AvatarRoot>
</template>
