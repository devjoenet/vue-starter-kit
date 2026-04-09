import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { normalizePermissionNames } from '@/lib/auth';

export function useAbility() {
  const page = usePage();
  const permissions = computed(() => normalizePermissionNames(page.props.auth?.permissions));
  const permissionSet = computed(() => new Set(permissions.value));

  const can = (permission: string) => permissionSet.value.has(permission);

  return {
    can,
    permissions,
  };
}
