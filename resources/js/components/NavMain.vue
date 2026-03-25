<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import SidebarGroup from '@/components/ui/sidebar/SidebarGroup.vue';
import SidebarGroupLabel from '@/components/ui/sidebar/SidebarGroupLabel.vue';
import SidebarMenu from '@/components/ui/sidebar/SidebarMenu.vue';
import SidebarMenuButton from '@/components/ui/sidebar/SidebarMenuButton.vue';
import SidebarMenuItem from '@/components/ui/sidebar/SidebarMenuItem.vue';
import { useCurrentUrl } from '@/composables/useCurrentUrl';
import type { NavItem } from '@/types/navigation';
const props = withDefaults(
  defineProps<{
    items: NavItem[];
    label?: string;
  }>(),
  {
    label: 'Platform',
  },
);

const { isCurrentUrl } = useCurrentUrl();
</script>

<template>
  <SidebarGroup class="px-2 py-0">
    <SidebarGroupLabel class="px-2 text-[0.68rem] font-semibold tracking-[0.18em] text-sidebar-foreground/70 uppercase">
      {{ props.label }}
    </SidebarGroupLabel>
    <SidebarMenu>
      <SidebarMenuItem v-for="item in items" :key="item.title">
        <SidebarMenuButton as-child :is-active="isCurrentUrl(item.href)" :tooltip="item.title">
          <Link :href="item.href" :prefetch="['hover', 'click']">
            <component :is="item.icon" />
            <span>{{ item.title }}</span>
          </Link>
        </SidebarMenuButton>
      </SidebarMenuItem>
    </SidebarMenu>
  </SidebarGroup>
</template>
