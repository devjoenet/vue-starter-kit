<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import Card from '@/components/ui/card/Card.vue';
import { cn } from '@/lib/utils';

const props = defineProps<{
  class?: HTMLAttributes['class'];
}>();
</script>

<template>
  <Card data-slot="admin-editor-aside-card" :class="cn('surface-editor-rail gap-4 px-5 py-5', props.class)">
    <slot />
  </Card>
</template>
