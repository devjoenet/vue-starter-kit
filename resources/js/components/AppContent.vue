<script setup lang="ts">
import { computed } from 'vue';
import { cn } from '@/lib/utils';
import SidebarInset from '@/components/ui/sidebar/SidebarInset.vue';
type Props = {
  variant?: 'header' | 'sidebar';
  class?: string;
};

const props = defineProps<Props>();
const className = computed(() => props.class);
</script>

<template>
  <SidebarInset
    v-if="props.variant === 'sidebar'"
    :class="cn('overflow-x-hidden bg-transparent pb-4 md:pb-6', className)"
  >
    <slot />
  </SidebarInset>
  <main
    v-else
    class="surface-shell-panel mx-auto flex h-full w-full max-w-7xl flex-1 flex-col gap-6 rounded-[1.25rem] p-5 lg:p-6"
    :class="className"
  >
    <slot />
  </main>
</template>
