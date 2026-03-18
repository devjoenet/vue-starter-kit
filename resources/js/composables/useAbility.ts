import { usePage } from '@inertiajs/vue3';

export function useAbility() {
  const page = usePage();
  const permissions = page.props.auth.permissions;

  const can = (permission: string) => permissions.includes(permission);

  return { can };
}
