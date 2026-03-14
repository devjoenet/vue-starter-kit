<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import Button from '@/components/ui/button/Button.vue';
import { useCurrentUrl } from '@/composables/useCurrentUrl';
import { toUrl } from '@/lib/utils';
import { edit as editAppearance } from '@/routes/appearance';
import { edit as editProfile } from '@/routes/profile';
import { show } from '@/routes/two-factor';
import { edit as editPassword } from '@/routes/user-password';
import type { NavItem } from '@/types/navigation';
const sidebarNavItems: NavItem[] = [
  {
    title: 'Profile',
    href: editProfile(),
  },
  {
    title: 'Password',
    href: editPassword(),
  },
  {
    title: 'Two-Factor Auth',
    href: show(),
  },
  {
    title: 'Appearance',
    href: editAppearance(),
  },
];

const { isCurrentUrl } = useCurrentUrl();
</script>

<template>
  <div class="space-y-6 px-4 py-6">
    <div class="flex flex-wrap items-start justify-between gap-3">
      <div class="space-y-1.5">
        <h1 class="text-2xl font-semibold tracking-tight">Settings</h1>
        <p class="max-w-2xl text-sm text-muted-foreground">
          Manage identity, security, and appearance preferences for your
          Southeast Code account.
        </p>
      </div>
    </div>

    <nav class="flex flex-wrap gap-2" aria-label="Settings">
      <Button
        v-for="item in sidebarNavItems"
        :key="toUrl(item.href)"
        :appearance="isCurrentUrl(item.href) ? 'filled' : 'outline'"
        :variant="isCurrentUrl(item.href) ? 'primary' : 'muted'"
        size="sm"
        as-child
      >
        <Link :href="item.href">
          {{ item.title }}
        </Link>
      </Button>
    </nav>

    <section class="max-w-3xl space-y-6">
      <slot />
    </section>
  </div>
</template>
