<script setup lang="ts">
  import { Link } from "@inertiajs/vue3";
  import { BookOpen, Folder, KeyRound, LayoutGrid, Shield, Users } from "lucide-vue-next";
  import { computed } from "vue";
  import NavFooter from "@/components/NavFooter.vue";
  import NavMain from "@/components/NavMain.vue";
  import NavUser from "@/components/NavUser.vue";
  import Sidebar from "@/components/ui/sidebar/Sidebar.vue";
  import SidebarContent from "@/components/ui/sidebar/SidebarContent.vue";
  import SidebarFooter from "@/components/ui/sidebar/SidebarFooter.vue";
  import SidebarHeader from "@/components/ui/sidebar/SidebarHeader.vue";
  import SidebarMenu from "@/components/ui/sidebar/SidebarMenu.vue";
  import SidebarMenuButton from "@/components/ui/sidebar/SidebarMenuButton.vue";
  import SidebarMenuItem from "@/components/ui/sidebar/SidebarMenuItem.vue";
  import { useAbility } from "@/composables/useAbility";
  import { dashboard } from "@/routes";
  import { index as adminPermissionsIndex } from "@/routes/admin/permissions";
  import { index as adminRolesIndex } from "@/routes/admin/roles";
  import { index as adminUsersIndex } from "@/routes/admin/users";
  import type { NavItem } from "@/types/navigation";
  import AppLogo from "./AppLogo.vue";

  const { can } = useAbility();

  const mainNavItems: NavItem[] = [
    {
      title: "Dashboard",
      href: dashboard(),
      icon: LayoutGrid,
    },
  ];

  const adminNavItems = computed<NavItem[]>(() => {
    return [
      ...(can("users.view")
        ? [
            {
              title: "Users",
              href: adminUsersIndex(),
              icon: Users,
            },
          ]
        : []),
      ...(can("roles.view")
        ? [
            {
              title: "Roles",
              href: adminRolesIndex(),
              icon: Shield,
            },
          ]
        : []),
      ...(can("permissions.view")
        ? [
            {
              title: "Permissions",
              href: adminPermissionsIndex(),
              icon: KeyRound,
            },
          ]
        : []),
    ];
  });

  const footerNavItems: NavItem[] = [
    {
      title: "Github Repo",
      href: "https://github.com/laravel/vue-starter-kit",
      icon: Folder,
    },
    {
      title: "Documentation",
      href: "https://laravel.com/docs/starter-kits#vue",
      icon: BookOpen,
    },
  ];
</script>

<template>
  <Sidebar collapsible="icon" variant="inset">
    <SidebarHeader>
      <SidebarMenu>
        <SidebarMenuItem>
          <SidebarMenuButton size="lg" as-child>
            <Link :href="dashboard()">
              <AppLogo />
            </Link>
          </SidebarMenuButton>
        </SidebarMenuItem>
      </SidebarMenu>
    </SidebarHeader>

    <SidebarContent>
      <NavMain :items="mainNavItems" label="Platform" />
      <NavMain v-if="adminNavItems.length" :items="adminNavItems" label="Admin" />
    </SidebarContent>

    <SidebarFooter>
      <NavFooter :items="footerNavItems" />
      <NavUser />
    </SidebarFooter>
  </Sidebar>
  <slot />
</template>
