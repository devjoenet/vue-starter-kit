import { setLayoutProps } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { dashboard } from '@/routes/admin';
import type { BreadcrumbItem } from '@/types/navigation';

export const adminPageLayout = AppLayout;
export const settingsPageLayout = [AppLayout, SettingsLayout];

export const setBreadcrumbs = (...breadcrumbs: BreadcrumbItem[]): void => {
  setLayoutProps({
    breadcrumbs,
  });
};

export const setAdminBreadcrumbs = (...breadcrumbs: BreadcrumbItem[]): void => {
  setBreadcrumbs({ title: 'Dashboard', href: dashboard.url() }, ...breadcrumbs);
};
