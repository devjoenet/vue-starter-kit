<script setup lang="ts">
  import { Link } from "@inertiajs/vue3";
  import { computed } from "vue";
  import { useAbility } from "@/composables/useAbility";
  import { dashboard } from "@/routes/admin/index";
  import AppLogoIcon from "@/components/AppLogoIcon.vue";
  import { index as adminPermissionsIndex } from "@/routes/admin/permissions/permissions";
  import { index as adminRolesIndex } from "@/routes/admin/roles";
  import { index as adminUsersIndex } from "@/routes/admin/users";

  defineProps<{ title: string }>();

  const { can } = useAbility();

  const nav = computed(() => {
    return [
      { label: "Dashboard", href: dashboard.url() },
      ...(can("users.view") ? [{ label: "Users", href: adminUsersIndex.url() }] : []),
      ...(can("roles.view") ? [{ label: "Roles", href: adminRolesIndex.url() }] : []),
      ...(can("permissions.view") ? [{ label: "Permissions", href: adminPermissionsIndex.url() }] : []),
    ];
  });
</script>

<template>
  <div class="min-h-screen">
    <header class="px-6 py-4">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <AppLogoIcon class="h-8 w-8" />
          <span class="font-semibold">Southeast Code</span>
        </div>
        <nav class="flex gap-4">
          <Link v-for="item in nav" :key="item.href" :href="item.href" class="text-sm opacity-90 hover:opacity-100">
            {{ item.label }}
          </Link>
        </nav>
      </div>
    </header>

    <main class="px-6 py-6">
      <slot />
    </main>
  </div>
</template>
