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
    class="shell-header-band m-4 mb-6 flex min-h-[4.5rem] shrink-0 items-center gap-3 rounded-[1.4rem] border border-sidebar-border/70 px-4 py-3 shadow-[var(--elevation-1)] transition-[background-color,opacity] duration-200 ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:min-h-12 motion-reduce:transition-none md:m-6 md:mb-6"
  >
    <div class="flex min-w-0 items-center gap-3">
      <SidebarTrigger class="-ml-1" />
      <div v-if="breadcrumbs && breadcrumbs.length > 0" class="min-w-0 space-y-1">
        <p class="text-[0.68rem] font-semibold tracking-[0.18em] text-muted-foreground/80 uppercase">Current surface</p>
        <Breadcrumbs :breadcrumbs="breadcrumbs" />
      </div>
    </div>
    <div class="ml-auto hidden rounded-full border border-sidebar-border/55 bg-background/18 px-3 py-1.5 text-[0.68rem] font-semibold tracking-[0.16em] text-muted-foreground uppercase sm:inline-flex">Workspace shell</div>
  </header>
</template>
