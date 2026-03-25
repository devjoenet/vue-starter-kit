<script setup lang="ts">
import type { PrimitiveProps } from 'reka-ui';
import type { HTMLAttributes } from 'vue';
import { Primitive } from 'reka-ui';
import { cn } from 'tailwind-variants';

const props = defineProps<
  PrimitiveProps & {
    class?: HTMLAttributes['class'];
  }
>();
</script>

<template>
  <Primitive
    data-slot="sidebar-group-action"
    data-sidebar="group-action"
    :as="as"
    :as-child="asChild"
    :class="
      cn(
        'motion-interactive-raise absolute top-2.5 right-2.5 flex size-8 items-center justify-center rounded-lg p-0 text-sidebar-foreground ring-sidebar-ring outline-hidden transition-transform hover:bg-sidebar-primary/14 hover:text-sidebar-primary focus-visible:ring-2 motion-reduce:transform-none [&>svg]:size-4 [&>svg]:shrink-0',
        'after:absolute after:-inset-1.5 md:after:hidden',
        'group-data-[collapsible=icon]:hidden',
        props.class,
      )
    "
  >
    <slot />
  </Primitive>
</template>
