<script setup lang="ts">
import { Monitor, Moon, Sun } from 'lucide-vue-next';
import { useAppearance } from '@/composables/useAppearance';

const { appearance, updateAppearance } = useAppearance();

const tabs = [
  { value: 'light', Icon: Sun, label: 'Light' },
  { value: 'dark', Icon: Moon, label: 'Dark' },
  { value: 'system', Icon: Monitor, label: 'System' },
] as const;
</script>

<template>
  <div class="inline-flex gap-1 rounded-xl border border-border/70 bg-muted/80 p-1 shadow-[var(--elevation-1)]">
    <button
      v-for="{ value, Icon, label } in tabs"
      :key="value"
      @click="updateAppearance(value)"
      :class="['flex items-center rounded-lg px-3.5 py-1.5 transition-colors', appearance === value ? 'bg-background text-foreground shadow-[var(--elevation-1)]' : 'text-muted-foreground hover:bg-background/70 hover:text-foreground']"
    >
      <component :is="Icon" class="-ml-1 h-4 w-4" />
      <span class="ml-1.5 text-sm">{{ label }}</span>
    </button>
  </div>
</template>
