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
  <div class="flex items-center justify-center gap-1.5 px-2 text-sm text-muted-foreground">
    <span>{{ prompt }}</span>
    <TextLink :id="linkId" :href="href" :as="as" :method="method">
      {{ label }}
    </TextLink>
  </div>
</template>
