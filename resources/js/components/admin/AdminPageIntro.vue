<script setup lang="ts">
import { cn } from 'tailwind-variants';
import type { HTMLAttributes } from 'vue';

defineProps<{
  asideClass?: HTMLAttributes['class'];
  class?: HTMLAttributes['class'];
  description: string;
  kicker?: string;
  title: string;
}>();
</script>

<template>
  <header :class="cn('flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between', $props.class)">
    <div class="max-w-3xl space-y-3">
      <p v-if="kicker" class="section-kicker">{{ kicker }}</p>
      <h1 class="text-3xl font-semibold tracking-tight text-balance sm:text-4xl">
        {{ title }}
      </h1>
      <p class="max-w-2xl text-sm leading-6 text-muted-foreground sm:text-base">
        {{ description }}
      </p>
    </div>

    <div v-if="$slots.aside" :class="cn('flex flex-wrap gap-2 lg:justify-end', asideClass)">
      <slot name="aside" />
    </div>
  </header>
</template>
