<script setup lang="ts">
  import { usePage } from "@inertiajs/vue3";
  import SidebarProvider from "@/components/ui/sidebar/SidebarProvider.vue";
  import type { AppShellVariant } from "@/types/ui";
  type Props = {
    variant?: AppShellVariant;
  };

  defineProps<Props>();

  const isOpen = usePage().props.sidebarOpen;
</script>

<template>
  <div v-if="variant === 'header'" class="flex min-h-screen w-full flex-col bg-gradient-to-br from-background via-background to-muted/60">
    <slot />
  </div>
  <SidebarProvider v-else :default-open="isOpen">
    <slot />
  </SidebarProvider>
</template>
