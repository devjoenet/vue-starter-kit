<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Menu } from 'lucide-vue-next';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import Button from '@/components/ui/button/Button.vue';
import Sheet from '@/components/ui/sheet/Sheet.vue';
import SheetContent from '@/components/ui/sheet/SheetContent.vue';
import SheetHeader from '@/components/ui/sheet/SheetHeader.vue';
import SheetTitle from '@/components/ui/sheet/SheetTitle.vue';
import SheetTrigger from '@/components/ui/sheet/SheetTrigger.vue';
import { useCurrentUrl } from '@/composables/useCurrentUrl';
import { toUrl } from '@/lib/utils';
import type { NavItem } from '@/types/navigation';

defineProps<{
  items: NavItem[];
  utilityItems: NavItem[];
}>();

const { whenCurrentUrl } = useCurrentUrl();
const activeItemStyles = 'bg-primary/10 text-primary';
</script>

<template>
  <div class="lg:hidden">
    <Sheet>
      <SheetTrigger :as-child="true">
        <Button
          appearance="ghost"
          size="icon"
          aria-label="Open navigation menu"
          class="mr-2 h-11 w-11"
        >
          <Menu class="h-5 w-5" />
        </Button>
      </SheetTrigger>
      <SheetContent side="left" class="w-75 border-sidebar-border/70 bg-sidebar p-6">
        <SheetTitle class="sr-only">Navigation Menu</SheetTitle>
        <SheetHeader class="flex justify-start text-left">
          <AppLogoIcon class="size-6 fill-current text-foreground" />
        </SheetHeader>
        <div class="flex h-full flex-1 flex-col justify-between space-y-4 py-6">
          <nav class="-mx-3 space-y-1">
            <Link
              v-for="item in items"
              :key="item.title"
              :href="item.href"
              class="flex min-h-11 items-center gap-x-3 rounded-xl border border-transparent px-3 py-3 text-sm font-medium transition-[background-color,border-color,color] duration-200 hover:border-primary/20 hover:bg-primary/10 hover:text-primary"
              :class="whenCurrentUrl(item.href, activeItemStyles)"
            >
              <component :is="item.icon" v-if="item.icon" class="h-5 w-5" />
              {{ item.title }}
            </Link>
          </nav>
          <div v-if="utilityItems.length" class="flex flex-col space-y-4">
            <a
              v-for="item in utilityItems"
              :key="item.title"
              :href="toUrl(item.href)"
              target="_blank"
              rel="noopener noreferrer"
              class="flex min-h-11 items-center space-x-2 rounded-lg px-3 py-3 text-sm font-medium"
            >
              <component :is="item.icon" v-if="item.icon" class="h-5 w-5" />
              <span>{{ item.title }}</span>
            </a>
          </div>
        </div>
      </SheetContent>
    </Sheet>
  </div>
</template>
