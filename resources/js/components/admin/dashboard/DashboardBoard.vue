<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import { computed } from 'vue';
import { cn } from 'tailwind-variants';
import { useAbility } from '@/composables/useAbility';
import { resolveDashboardWidgets } from '@/lib/admin-dashboard';
import type { AdminDashboardSources, DashboardWidgetSchema } from '@/types/admin/dashboard';

const props = defineProps<{
  class?: HTMLAttributes['class'];
  schema: DashboardWidgetSchema[];
  sources: AdminDashboardSources;
}>();

const { can } = useAbility();

const widgets = computed(() => {
  return resolveDashboardWidgets(props.schema, {
    sources: props.sources,
    can,
  });
});
</script>

<template>
  <section id="admin-dashboard-board" :class="cn('grid grid-cols-12 gap-4 xl:gap-5', props.class)" aria-label="Dashboard board">
    <div v-for="widget in widgets" :id="`admin-dashboard-widget-${widget.id}`" :key="widget.id" :data-dashboard-widget="widget.widget" :class="widget.className">
      <component :is="widget.component" v-bind="widget.props" />
    </div>
  </section>
</template>
