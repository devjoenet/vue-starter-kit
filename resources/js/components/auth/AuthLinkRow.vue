<script setup lang="ts">
import type { LinkComponentBaseProps, Method } from '@inertiajs/core';
import TextLink from '@/components/TextLink.vue';

withDefaults(
  defineProps<{
    as?: string;
    href: LinkComponentBaseProps['href'];
    label: string;
    linkId?: string;
    method?: Method;
    prompt: string;
  }>(),
  {
    as: 'a',
  },
);
</script>

<template>
  <div class="mx-auto flex w-fit flex-wrap items-center justify-center gap-1.5 rounded-full border border-border/45 bg-background/20 px-4 py-2.5 text-sm text-muted-foreground shadow-(--elevation-1) backdrop-blur-xl">
    <span>{{ prompt }}</span>
    <TextLink :id="linkId" :href="href" :as="as" :method="method">
      {{ label }}
    </TextLink>
  </div>
</template>
