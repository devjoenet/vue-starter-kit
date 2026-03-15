<script setup lang="ts">
import type { PrimitiveProps } from 'reka-ui';
import type { HTMLAttributes } from 'vue';
import { Primitive } from 'reka-ui';
import { cn } from '@/lib/utils';

const props = withDefaults(
  defineProps<
    PrimitiveProps & {
      showOnHover?: boolean;
      class?: HTMLAttributes['class'];
    }
  >(),
  {
    as: 'button',
  },
);
</script>

<template>
  <Primitive
    data-slot="sidebar-menu-action"
    data-sidebar="menu-action"
    :class="
      cn(
        'motion-interactive-raise absolute top-1 right-1 flex size-8 items-center justify-center rounded-lg p-0 text-sidebar-foreground ring-sidebar-ring outline-hidden transition-transform peer-hover/menu-button:text-sidebar-primary hover:bg-sidebar-primary/14 hover:text-sidebar-primary focus-visible:ring-2 motion-reduce:transform-none [&>svg]:size-4 [&>svg]:shrink-0',
        'after:absolute after:-inset-1.5 md:after:hidden',
        'peer-data-[size=sm]/menu-button:top-1',
        'peer-data-[size=default]/menu-button:top-1',
        'peer-data-[size=lg]/menu-button:top-2',
        'group-data-[collapsible=icon]:hidden',
        showOnHover &&
          'group-focus-within/menu-item:opacity-100 group-hover/menu-item:opacity-100 peer-data-[active=true]/menu-button:text-sidebar-primary-foreground data-[state=open]:opacity-100 md:opacity-0',
        props.class,
      )
    "
    :as="as"
    :as-child="asChild"
  >
    <slot />
  </Primitive>
</template>
