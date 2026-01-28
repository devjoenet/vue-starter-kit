import { usePage } from '@inertiajs/vue3';


export function useAbility() {
    const page = usePage();
    const permissions = (page.props.auth?.permissions ?? []) as string[];

    const can = (perm: string) => permissions.includes(perm);

    return { can };
}
