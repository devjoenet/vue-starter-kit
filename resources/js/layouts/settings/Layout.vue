<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import Button from '@/components/ui/button/Button.vue';
import { useCurrentUrl } from '@/composables/useCurrentUrl';
import { toUrl } from '@/lib/utils';
import { edit as editAppearance } from '@/routes/appearance';
import { edit as editProfile } from '@/routes/profile';
import { show } from '@/routes/two-factor';
import { edit as editPassword } from '@/routes/user-password';
import type { NavItem } from '@/types/navigation';

type SettingsNavItem = NavItem & {
  description: string;
  heading: string;
};

const settingsNavItems: SettingsNavItem[] = [
  {
    title: 'Profile',
    href: editProfile(),
    heading: 'Profile settings',
    description:
      'Update the identity and contact details attached to this account.',
  },
  {
    title: 'Password',
    href: editPassword(),
    heading: 'Password settings',
    description:
      'Change the credential used for sign-in and keep access secure.',
  },
  {
    title: 'Two-Factor Auth',
    href: show(),
    heading: 'Two-factor authentication',
    description:
      'Protect sign-in with an authenticator app and a dependable recovery path.',
  },
  {
    title: 'Appearance',
    href: editAppearance(),
    heading: 'Appearance settings',
    description:
      'Choose the light, dark, or system presentation for day-to-day use.',
  },
];

const { isCurrentUrl } = useCurrentUrl();

const activeNavItem = computed(
  () =>
    settingsNavItems.find((item) => isCurrentUrl(item.href)) ??
    settingsNavItems[0],
);
</script>

<template>
  <div
    id="settings-layout"
    class="surface-settings-shell motion-stage relative space-y-6 overflow-hidden rounded-[1.75rem] px-4 py-6 sm:px-6"
  >
    <div
      class="pointer-events-none absolute inset-x-0 top-0 h-64 bg-linear-to-b from-secondary/18 via-secondary/6 to-transparent"
    />
    <div
      class="pointer-events-none absolute top-8 -right-14 size-64 rounded-full bg-accent/14 blur-3xl"
    />
    <div
      class="pointer-events-none absolute -bottom-24 left-8 size-72 rounded-full bg-primary/12 blur-3xl"
    />

    <div class="relative space-y-6">
      <header
        id="settings-layout-header"
        class="motion-step max-w-3xl space-y-3"
        style="--motion-order: 0"
      >
        <p class="section-kicker">Settings workspace</p>
        <h1 class="text-3xl font-semibold tracking-tight text-balance">
          {{ activeNavItem.heading }}
        </h1>
        <p class="text-sm leading-6 text-muted-foreground">
          {{ activeNavItem.description }}
        </p>
      </header>

      <nav
        id="settings-layout-nav"
        class="surface-settings-nav motion-step flex flex-col gap-2 rounded-[1.5rem] p-3 sm:flex-row sm:flex-wrap"
        style="--motion-order: 1"
        aria-label="Settings"
      >
        <Button
          v-for="item in settingsNavItems"
          :key="toUrl(item.href)"
          :appearance="isCurrentUrl(item.href) ? 'filled' : 'outline'"
          :variant="isCurrentUrl(item.href) ? 'primary' : 'muted'"
          rounded="xl"
          as-child
          class="motion-sheen min-h-11 justify-start px-4"
        >
          <Link :href="item.href">
            {{ item.title }}
          </Link>
        </Button>
      </nav>

      <section
        class="motion-step w-full max-w-4xl space-y-6"
        style="--motion-order: 2"
      >
        <slot />
      </section>
    </div>
  </div>
</template>
