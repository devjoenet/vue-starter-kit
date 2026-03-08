import { usePage } from '@inertiajs/vue3';
import type { AdminPermission } from '@/types/wayfinder-generated';

export function useAbility() {
  const page = usePage();
  const permissions = page.props.auth.permissions;

  const can = (permission: AdminPermission) => permissions.includes(permission);

  return { can };
}
