<script setup lang="ts">
  import type { HTMLAttributes } from "vue";
  import { cn } from "@/lib/utils";

  const props = defineProps<{
    class?: HTMLAttributes["class"];
  }>();
</script>

<template>
  <div data-slot="card" :class="cn('flex flex-col gap-6 rounded-[var(--radius-lg)] border border-[color:var(--outline)] bg-[var(--surface)] py-6 text-[var(--foreground)] shadow-[var(--elevation-1)]', props.class)">
    <slot />
  </div>
</template>
