<script setup lang="ts">
  import { computed } from "vue";
  import SidebarInset from "@/components/ui/sidebar/SidebarInset.vue";
  type Props = {
    variant?: "header" | "sidebar";
    class?: string;
  };

  const props = defineProps<Props>();
  const className = computed(() => props.class);
</script>

<template>
  <SidebarInset v-if="props.variant === 'sidebar'" :class="className">
    <slot />
  </SidebarInset>
  <main v-else class="mx-auto flex h-full w-full max-w-7xl flex-1 flex-col gap-4 rounded-xl bg-gradient-to-br from-background via-background to-muted/60 p-6" :class="className">
    <slot />
  </main>
</template>
