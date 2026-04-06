<script setup lang="ts">
import { cn } from 'tailwind-variants';
import { computed } from 'vue';
import SidebarInset from '@/components/ui/sidebar/SidebarInset.vue';
import { surfaceSectionClassNames } from '@/components/ui/surface/variants';
import type { AppSurfaceVariant } from '@/types/ui';

type Props = {
  variant?: 'header' | 'sidebar';
  class?: string;
  surfaceVariant?: AppSurfaceVariant;
};

const props = defineProps<Props>();
const className = computed(() => props.class);
</script>

<template>
  <SidebarInset v-if="props.variant === 'sidebar'" :surface-variant="props.surfaceVariant" :class="cn('overflow-x-hidden pb-4 md:pb-6', props.surfaceVariant === 'dashboard' ? surfaceSectionClassNames.workspaceShell : 'bg-transparent', className)">
    <slot />
  </SidebarInset>
  <main v-else :class="cn(surfaceSectionClassNames.workspacePanel, 'mx-auto flex h-full w-full max-w-7xl flex-1 flex-col gap-6 rounded-[1.25rem] p-5 lg:p-6', className)">
    <slot />
  </main>
</template>
