<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { cn } from '@/lib/utils';

const props = defineProps<{
  class?: HTMLAttributes['class'];
}>();
</script>

<template>
  <div data-slot="surface-brand-lockup" :class="cn('flex items-center gap-3 sm:gap-4', props.class)">
    <AppLogoIcon class="motion-brand-mark size-10 shrink-0 sm:size-12 lg:size-14" gaOutlineClass="fill-primary" codeIconClass="fill-secondary" />
    <p class="text-[clamp(1.45rem,2.3vw,2.7rem)] leading-none font-semibold tracking-[0.12em] whitespace-nowrap uppercase">Southeast Code</p>
  </div>
</template>
