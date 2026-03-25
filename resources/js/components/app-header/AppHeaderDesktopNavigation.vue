<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import NavigationMenu from '@/components/ui/navigation-menu/NavigationMenu.vue';
import NavigationMenuItem from '@/components/ui/navigation-menu/NavigationMenuItem.vue';
import NavigationMenuList from '@/components/ui/navigation-menu/NavigationMenuList.vue';
import { navigationMenuTriggerStyle } from '@/components/ui/navigation-menu/variants';
import { useCurrentUrl } from '@/composables/useCurrentUrl';
import type { NavItem } from '@/types/navigation';

defineProps<{
  items: NavItem[];
}>();

const { isCurrentUrl, whenCurrentUrl } = useCurrentUrl();
const activeItemStyles = 'bg-primary/10 text-primary';
</script>

<template>
  <div class="hidden h-full lg:flex lg:flex-1">
    <NavigationMenu class="ml-10 flex h-full items-stretch">
      <NavigationMenuList class="flex h-full items-stretch space-x-2">
        <NavigationMenuItem v-for="item in items" :key="item.title" class="relative flex h-full items-center">
          <Link :class="[navigationMenuTriggerStyle(), whenCurrentUrl(item.href, activeItemStyles), 'cursor-pointer rounded-full border border-transparent px-4 hover:border-primary/18 hover:bg-primary/6']" :href="item.href" :prefetch="['hover', 'click']">
            <component :is="item.icon" v-if="item.icon" class="mr-2 h-4 w-4" />
            {{ item.title }}
          </Link>
          <div v-if="isCurrentUrl(item.href)" class="absolute bottom-0 left-0 h-0.5 w-full translate-y-px bg-primary" />
        </NavigationMenuItem>
      </NavigationMenuList>
    </NavigationMenu>
  </div>
</template>
