import { usePage } from '@inertiajs/vue3';
import type { AdminPermission } from '@/types/admin-permissions';

export function useAbility() {
  const page = usePage();
  const permissions = page.props.auth.permissions;

  const can = (permission: AdminPermission) => permissions.includes(permission);

  return { can };
}
