<script setup lang="ts">
import AppToastItem from '@/components/toasts/AppToastItem.vue';
import { useAppToastFeed } from '@/composables/useAppToastFeed';

const { toasts, dismissToast } = useAppToastFeed();
</script>

<template>
  <Teleport to="body">
    <div
      class="pointer-events-none fixed top-4 right-4 z-[200] flex w-[min(94vw,18rem)] flex-col gap-2"
    >
      <TransitionGroup name="toast" tag="div" class="space-y-2">
        <AppToastItem
          v-for="item in toasts"
          :key="item.id"
          :item="item"
          @dismiss="dismissToast"
        />
      </TransitionGroup>
    </div>
  </Teleport>
</template>

<style scoped>
.toast-enter-active,
.toast-leave-active {
  transition: all 180ms ease;
}

.toast-enter-from,
.toast-leave-to {
  opacity: 0;
  transform: translateY(-8px) scale(0.98);
}
</style>
