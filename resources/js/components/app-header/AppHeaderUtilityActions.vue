<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import DropdownMenu from '@/components/ui/dropdown-menu/DropdownMenu.vue';
import DropdownMenuContent from '@/components/ui/dropdown-menu/DropdownMenuContent.vue';
import DropdownMenuTrigger from '@/components/ui/dropdown-menu/DropdownMenuTrigger.vue';
import Tooltip from '@/components/ui/tooltip/Tooltip.vue';
import TooltipContent from '@/components/ui/tooltip/TooltipContent.vue';
import TooltipProvider from '@/components/ui/tooltip/TooltipProvider.vue';
import TooltipTrigger from '@/components/ui/tooltip/TooltipTrigger.vue';
import Avatar from '@/components/ui/avatar/Avatar.vue';
import AvatarFallback from '@/components/ui/avatar/AvatarFallback.vue';
import AvatarImage from '@/components/ui/avatar/AvatarImage.vue';
import UserMenuContent from '@/components/UserMenuContent.vue';
import { getInitials } from '@/composables/useInitials';
import { toUrl } from '@/lib/utils';
import type { User } from '@/types/auth';
import type { NavItem } from '@/types/navigation';
import { Search } from 'lucide-vue-next';

defineProps<{
  items: NavItem[];
  user: User | null;
}>();
</script>

<template>
  <div class="ml-auto flex items-center space-x-2">
    <div class="relative flex items-center space-x-1">
      <Button appearance="ghost" size="icon" class="group h-9 w-9 cursor-pointer">
        <Search class="size-5 opacity-80 group-hover:opacity-100" />
      </Button>

      <div class="hidden space-x-1 lg:flex">
        <template v-for="item in items" :key="item.title">
          <TooltipProvider :delay-duration="0">
            <Tooltip>
              <TooltipTrigger>
                <Button
                  appearance="ghost"
                  size="icon"
                  as-child
                  class="group h-9 w-9 cursor-pointer"
                >
                  <a :href="toUrl(item.href)" target="_blank" rel="noopener noreferrer">
                    <span class="sr-only">{{ item.title }}</span>
                    <component
                      :is="item.icon"
                      v-if="item.icon"
                      class="size-5 opacity-80 group-hover:opacity-100"
                    />
                  </a>
                </Button>
              </TooltipTrigger>
              <TooltipContent>
                <p>{{ item.title }}</p>
              </TooltipContent>
            </Tooltip>
          </TooltipProvider>
        </template>
      </div>
    </div>

    <DropdownMenu v-if="user">
      <DropdownMenuTrigger :as-child="true">
        <Button
          appearance="ghost"
          size="icon"
          class="relative size-10 w-auto rounded-full p-1 focus-within:ring-2 focus-within:ring-primary"
        >
          <Avatar class="size-8 overflow-hidden rounded-full">
            <AvatarImage v-if="user.avatar" :src="user.avatar" :alt="user.name" />
            <AvatarFallback
              class="rounded-lg bg-neutral-200 font-semibold text-black dark:bg-neutral-700 dark:text-white"
            >
              {{ getInitials(user.name) }}
            </AvatarFallback>
          </Avatar>
        </Button>
      </DropdownMenuTrigger>
      <DropdownMenuContent align="end" class="w-56">
        <UserMenuContent :user="user" />
      </DropdownMenuContent>
    </DropdownMenu>
  </div>
</template>
