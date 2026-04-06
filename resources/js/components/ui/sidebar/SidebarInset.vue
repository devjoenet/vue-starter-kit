<script setup lang="ts">
import { surfaceSectionClassNames } from '@/components/ui/surface/variants';
import type { AppSurfaceVariant } from '@/types/ui';
import type { HTMLAttributes } from 'vue';
import { cn } from 'tailwind-variants';

const props = defineProps<{
  class?: HTMLAttributes['class'];
  surfaceVariant?: AppSurfaceVariant;
}>();
</script>

<template>
  <main
    data-slot="sidebar-inset"
    :class="
      cn(
        props.surfaceVariant === 'dashboard'
          ? 'relative flex w-full flex-1 flex-col md:peer-data-[variant=inset]:m-0 md:peer-data-[variant=inset]:rounded-none md:peer-data-[variant=inset]:shadow-none md:peer-data-[variant=inset]:peer-data-[state=collapsed]:ml-0'
          : cn(
              surfaceSectionClassNames.workspacePanel,
              'relative flex w-full flex-1 flex-col md:peer-data-[variant=inset]:m-2 md:peer-data-[variant=inset]:ml-0 md:peer-data-[variant=inset]:rounded-xl md:peer-data-[variant=inset]:peer-data-[state=collapsed]:ml-2',
            ),
        props.class,
      )
    "
  >
    <slot />
  </main>
</template>
