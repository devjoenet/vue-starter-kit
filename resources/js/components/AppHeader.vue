<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { LayoutGrid } from 'lucide-vue-next';
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

const utilityNavItems: NavItem[] = [];
</script>

<template>
  <div>
    <div class="shell-header-band border-b border-sidebar-border/70">
      <div class="mx-auto flex min-h-[4.5rem] items-center gap-4 px-4 py-3 md:max-w-7xl">
        <AppHeaderMobileNavigation :items="mainNavItems" :utility-items="utilityNavItems" />

        <Link :href="dashboard()" class="flex min-w-0 items-center gap-x-3 rounded-[1.25rem] border border-sidebar-border/60 bg-background/24 px-3 py-2">
          <AppLogo />
          <div class="hidden min-w-0 xl:block">
            <p class="text-[0.68rem] font-semibold tracking-[0.18em] text-muted-foreground uppercase">Southeast Code</p>
            <p class="text-sm leading-6 text-foreground/88">Custom business apps and client-ready demos</p>
          </div>
        </Link>

        <AppHeaderDesktopNavigation :items="mainNavItems" />

        <div class="hidden rounded-full border border-sidebar-border/55 bg-background/20 px-4 py-2 text-[0.68rem] font-semibold tracking-[0.16em] text-muted-foreground uppercase lg:inline-flex">Client-ready system</div>

        <AppHeaderUtilityActions :items="utilityNavItems" :user="user" />
      </div>
    </div>

    <div v-if="props.breadcrumbs.length > 1" class="flex w-full border-b border-sidebar-border/60">
      <div class="mx-auto flex min-h-12 w-full items-center justify-start px-4 text-muted-foreground md:max-w-7xl">
        <div class="flex min-w-0 items-center gap-3">
          <span class="hidden text-[0.68rem] font-semibold tracking-[0.18em] text-muted-foreground/80 uppercase sm:block"> Current surface </span>
          <Breadcrumbs :breadcrumbs="breadcrumbs" />
        </div>
      </div>
    </div>
  </div>
</template>
