<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import AdminIndexPageHeader from '@/components/admin/AdminIndexPageHeader.vue';
import PermissionIndexTable from '@/components/admin/PermissionIndexTable.vue';
import Button from '@/components/ui/button/Button.vue';
import { useAbility } from '@/composables/useAbility';
import { adminPageLayout, setAdminBreadcrumbs } from '@/lib/page-layouts';
import { create, index } from '@/routes/admin/permissions';
import { adminPermissions } from '@/types/admin-permissions';
import type { AdminPermissionsIndexPageProps } from '@/types/page-props';
defineOptions({
  layout: adminPageLayout,
});

setAdminBreadcrumbs({ title: 'Permissions', href: index.url() });

const props = defineProps<AdminPermissionsIndexPageProps>();

const { can } = useAbility();
const canCreate = computed(() => can(adminPermissions.permissionsCreate));
const canUpdate = computed(() => can(adminPermissions.permissionsUpdate));
</script>

<template>
  <div id="admin-permissions-index-page" class="motion-stage space-y-6 px-4">
    <AdminIndexPageHeader id="admin-permissions-index-page-header" title="Permissions" style="--motion-order: 0">
      <template #actions>
        <Button v-if="canCreate" id="admin-permissions-index-create-button" appearance="outline" as-child class="motion-sheen">
          <Link :href="create.url()">Create New Permission</Link>
        </Button>
      </template>
    </AdminIndexPageHeader>

    <div class="motion-step" style="--motion-order: 1">
      <PermissionIndexTable id="admin-permissions-index-table-card" :can-update="canUpdate" :filter-options="props.filterOptions" :groups="props.groups" :permissions="props.permissions" :query="props.query" />
    </div>
  </div>
</template>
