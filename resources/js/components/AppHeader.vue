<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { BookOpen, Folder, LayoutGrid } from 'lucide-vue-next';
import { computed } from 'vue';
import AppHeaderDesktopNavigation from '@/components/app-header/AppHeaderDesktopNavigation.vue';
import AppHeaderMobileNavigation from '@/components/app-header/AppHeaderMobileNavigation.vue';
import AppHeaderUtilityActions from '@/components/app-header/AppHeaderUtilityActions.vue';
import AppLogo from '@/components/AppLogo.vue';
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import { dashboard } from '@/routes';
import type { BreadcrumbItem, NavItem } from '@/types/navigation';

type Props = {
  breadcrumbs?: BreadcrumbItem[];
};

const props = withDefaults(defineProps<Props>(), {
  breadcrumbs: () => [],
});

const page = usePage();
const user = computed(() => page.props.auth?.user ?? null);

const mainNavItems: NavItem[] = [
  {
    title: 'Dashboard',
    href: dashboard(),
    icon: LayoutGrid,
  },
];

const rightNavItems: NavItem[] = [
  {
    title: 'Repository',
    href: 'https://github.com/laravel/vue-starter-kit',
    icon: Folder,
  },
  {
    title: 'Documentation',
    href: 'https://laravel.com/docs/starter-kits#vue',
    icon: BookOpen,
  },
];
</script>

<template>
  <div>
    <div class="border-b border-sidebar-border/80">
      <div class="mx-auto flex h-16 items-center px-4 md:max-w-7xl">
        <AppHeaderMobileNavigation
          :items="mainNavItems"
          :utility-items="rightNavItems"
        />

        <Link :href="dashboard()" class="flex items-center gap-x-2">
          <AppLogo />
        </Link>

        <AppHeaderDesktopNavigation :items="mainNavItems" />

        <AppHeaderUtilityActions :items="rightNavItems" :user="user" />
      </div>
    </div>

    <div
      v-if="props.breadcrumbs.length > 1"
      class="flex w-full border-b border-sidebar-border/70"
    >
      <div
        class="mx-auto flex h-12 w-full items-center justify-start px-4 text-neutral-500 md:max-w-7xl"
      >
        <Breadcrumbs :breadcrumbs="breadcrumbs" />
      </div>
    </div>
  </div>
</template>
