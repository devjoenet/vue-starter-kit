<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { KeyRound, LayoutGrid, Shield, Users } from 'lucide-vue-next';
import { computed } from 'vue';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import Sidebar from '@/components/ui/sidebar/Sidebar.vue';
import SidebarContent from '@/components/ui/sidebar/SidebarContent.vue';
import SidebarFooter from '@/components/ui/sidebar/SidebarFooter.vue';
import SidebarHeader from '@/components/ui/sidebar/SidebarHeader.vue';
import SidebarMenu from '@/components/ui/sidebar/SidebarMenu.vue';
import SidebarMenuButton from '@/components/ui/sidebar/SidebarMenuButton.vue';
import SidebarMenuItem from '@/components/ui/sidebar/SidebarMenuItem.vue';
import { useAbility } from '@/composables/useAbility';
import { dashboard } from '@/routes';
import { index as adminPermissionsIndex } from '@/routes/admin/permissions';
import { index as adminRolesIndex } from '@/routes/admin/roles';
import { index as adminUsersIndex } from '@/routes/admin/users';
import { adminPermissions } from '@/types/admin-permissions';
import type { NavItem } from '@/types/navigation';
import AppLogo from './AppLogo.vue';

const { can } = useAbility();

const mainNavItems: NavItem[] = [
  {
    title: 'Dashboard',
    href: dashboard(),
    icon: LayoutGrid,
  },
];

const adminNavItems = computed<NavItem[]>(() => {
  return [
    ...(can(adminPermissions.usersView)
      ? [
          {
            title: 'Users',
            href: adminUsersIndex(),
            icon: Users,
          },
        ]
      : []),
    ...(can(adminPermissions.rolesView)
      ? [
          {
            title: 'Roles',
            href: adminRolesIndex(),
            icon: Shield,
          },
        ]
      : []),
    ...(can(adminPermissions.permissionsView)
      ? [
          {
            title: 'Permissions',
            href: adminPermissionsIndex(),
            icon: KeyRound,
          },
        ]
      : []),
  ];
});

const footerNavItems: NavItem[] = [];
</script>

<template>
  <Sidebar collapsible="icon" variant="inset">
    <SidebarHeader class="gap-4 border-b border-sidebar-border/70 pb-4">
      <SidebarMenu>
        <SidebarMenuItem>
          <SidebarMenuButton size="lg" as-child>
            <Link :href="dashboard()">
              <AppLogo />
            </Link>
          </SidebarMenuButton>
        </SidebarMenuItem>
      </SidebarMenu>

      <div class="group-data-[collapsible=icon]:hidden">
        <div class="relative overflow-hidden rounded-[1.5rem] border border-sidebar-border/70 bg-sidebar-accent/55 px-4 py-4 shadow-[var(--elevation-1)]">
          <div class="pointer-events-none absolute inset-y-4 right-4 w-[0.1875rem] rounded-full bg-linear-to-b from-secondary via-primary to-accent opacity-70" />
          <p class="text-[0.68rem] font-semibold tracking-[0.18em] text-sidebar-foreground/70 uppercase">Workspace</p>
          <p class="mt-1 pr-6 text-sm font-semibold">Southeast Code</p>
          <p class="mt-1 pr-6 text-xs leading-5 text-sidebar-foreground/75">Access control, settings, and starter surfaces shaped for client-ready demos.</p>
        </div>
      </div>
    </SidebarHeader>

    <SidebarContent class="gap-4 pt-3">
      <NavMain :items="mainNavItems" label="Platform" />
      <NavMain v-if="adminNavItems.length" :items="adminNavItems" label="Admin" />
    </SidebarContent>

    <SidebarFooter>
      <template v-if="footerNavItems.length">
        <NavFooter :items="footerNavItems" />
      </template>
      <NavUser />
    </SidebarFooter>
  </Sidebar>
  <slot />
</template>
