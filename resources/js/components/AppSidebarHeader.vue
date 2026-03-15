<script setup lang="ts">
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import SidebarTrigger from '@/components/ui/sidebar/SidebarTrigger.vue';
import type { BreadcrumbItem } from '@/types/navigation';
withDefaults(
  defineProps<{
    breadcrumbs?: BreadcrumbItem[];
  }>(),
  {
    breadcrumbs: () => [],
  },
);
</script>

<template>
  <header
    class="shell-header-band mb-6 flex h-16 shrink-0 items-center gap-2 border-b border-sidebar-border/70 px-6 transition-[background-color,opacity] duration-200 ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-12 motion-reduce:transition-none md:px-4"
  >
    <div class="flex items-center gap-2">
      <SidebarTrigger class="-ml-1" />
      <template v-if="breadcrumbs && breadcrumbs.length > 0">
        <Breadcrumbs :breadcrumbs="breadcrumbs" />
      </template>
    </div>
  </header>
</template>
