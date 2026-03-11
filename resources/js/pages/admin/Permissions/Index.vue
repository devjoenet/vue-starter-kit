<script setup lang="ts">
import { Link, setLayoutProps } from '@inertiajs/vue3';
import { computed } from 'vue';
import PermissionIndexTable from '@/components/admin/PermissionIndexTable.vue';
import Button from '@/components/ui/button/Button.vue';
import { useAbility } from '@/composables/useAbility';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes/admin';
import { create, index } from '@/routes/admin/permissions';
import { adminPermissions } from '@/types/admin-permissions';
import type { AdminPermissionsIndexPageProps } from '@/types/page-props';
defineOptions({
  layout: AppLayout,
});

setLayoutProps({
  breadcrumbs: [
    { title: 'Dashboard', href: dashboard.url() },
    { title: 'Permissions', href: index.url() },
  ],
});

const props = defineProps<AdminPermissionsIndexPageProps>();

const { can } = useAbility();
const canCreate = computed(() => can(adminPermissions.permissionsCreate));
const canUpdate = computed(() => can(adminPermissions.permissionsUpdate));
</script>

<template>
  <div id="admin-permissions-index-page" class="space-y-6 px-4">
    <div
      id="admin-permissions-index-page-header"
      class="flex flex-wrap items-center justify-between gap-3"
    >
      <h1 class="text-2xl font-semibold">Permissions</h1>

      <Button
        v-if="canCreate"
        id="admin-permissions-index-create-button"
        appearance="outline"
        as-child
      >
        <Link :href="create.url()">Create New Permission</Link>
      </Button>
    </div>

    <PermissionIndexTable
      id="admin-permissions-index-table-card"
      :can-update="canUpdate"
      :filter-options="props.filterOptions"
      :permissions="props.permissions"
      :query="props.query"
    />
  </div>
</template>
