<script setup lang="ts">
  import type { HTMLAttributes } from "vue";
  import { PanelLeftClose, PanelLeftOpen } from "lucide-vue-next";
  import { cn } from "@/lib/utils";
  import Button from "@/components/ui/button/Button.vue";
  import { sidebarTriggerVariants } from "./styles";
  import { useSidebar } from "./utils";

  const props = defineProps<{
    class?: HTMLAttributes["class"];
  }>();

  const { isMobile, state, toggleSidebar } = useSidebar();
</script>

<template>
  <Button data-sidebar="trigger" data-slot="sidebar-trigger" appearance="ghost" size="icon" :class="cn(sidebarTriggerVariants(), props.class)" @click="toggleSidebar">
    <PanelLeftOpen v-if="isMobile || state === 'collapsed'" />
    <PanelLeftClose v-else />
    <span class="sr-only">Toggle Sidebar</span>
  </Button>
</template>
