import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';


export function useAbility() {
    const page = usePage();
    const permissions = computed(() => (page.props.value.auth?.permissions ?? []) as string[]);

    const can = (perm: string) => permissions.value.includes(perm);

    return { can };
}
