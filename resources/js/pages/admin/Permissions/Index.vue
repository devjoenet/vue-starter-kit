<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { computed, h } from 'vue';
import PermissionIndexTable from '@/components/admin/PermissionIndexTable.vue';
import Button from '@/components/ui/button/Button.vue';
import { useAbility } from '@/composables/useAbility';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes/admin';
import { create, index } from '@/routes/admin/permissions';
import { adminPermissions } from '@/types/admin-permissions';
import type { AdminPermissionsIndexPageProps } from '@/types/page-props';
defineOptions({
  layout: (_: unknown, page: unknown) =>
    h(
      AppLayout,
      {
        breadcrumbs: [
          { title: 'Dashboard', href: dashboard.url() },
          { title: 'Permissions', href: index.url() },
        ],
      },
      () => page,
    ),
});

const props = defineProps<AdminPermissionsIndexPageProps>();

const { can } = useAbility();
const canCreate = computed(() => can(adminPermissions.permissionsCreate));
const canUpdate = computed(() => can(adminPermissions.permissionsUpdate));
</script>

<template>
  <div class="space-y-6 px-4">
    <div class="flex flex-wrap items-center justify-between gap-3">
      <h1 class="text-2xl font-semibold">Permissions</h1>

      <Button v-if="canCreate" appearance="outline" as-child>
        <Link :href="create.url()">Create New Permission</Link>
      </Button>
    </div>

    <PermissionIndexTable
      :can-update="canUpdate"
      :filter-options="props.filterOptions"
      :permissions="props.permissions"
      :query="props.query"
    />
  </div>
</template>
