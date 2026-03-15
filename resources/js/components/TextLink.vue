<script setup lang="ts">
import type { LinkComponentBaseProps, Method } from '@inertiajs/core';
import { Link } from '@inertiajs/vue3';

type Props = {
  href: LinkComponentBaseProps['href'];
  method?: Method;
  as?: string;
};

defineProps<Props>();
</script>

<template>
  <Link
    :href="href"
    :method="method"
    :as="as"
    class="text-foreground underline decoration-border underline-offset-4 transition-colors duration-300 ease-out hover:text-primary hover:decoration-primary/50"
  >
    <slot />
  </Link>
</template>
