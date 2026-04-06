import type { Component } from 'vue';
import DashboardActionWidget from '@/components/admin/dashboard/DashboardActionWidget.vue';
import DashboardAnchorWidget from '@/components/admin/dashboard/DashboardAnchorWidget.vue';
import DashboardChartWidget from '@/components/admin/dashboard/DashboardChartWidget.vue';
import DashboardStatWidget from '@/components/admin/dashboard/DashboardStatWidget.vue';
import DashboardSupportWidget from '@/components/admin/dashboard/DashboardSupportWidget.vue';
import { index as adminPermissionsIndex } from '@/routes/admin/permissions';
import { index as adminRolesIndex } from '@/routes/admin/roles';
import { index as adminUsersIndex } from '@/routes/admin/users';
import type { AdminDashboardSources, DashboardActionSourceKey, DashboardResolvedWidget, DashboardWidgetSchema, DashboardWidgetSize, DashboardWidgetVariant } from '@/types/admin/dashboard';
import { adminPermissions } from '@/types/admin-permissions';

type DashboardResolverContext = {
  can: (permission: string) => boolean;
  sources: AdminDashboardSources;
};

type DashboardWidgetResolver = (schema: DashboardWidgetSchema, context: DashboardResolverContext) => DashboardResolvedWidget | null;

type DashboardActionConfig = {
  ctaLabel: string;
  href: string;
  permission: string;
  variant: DashboardWidgetVariant;
};

const widgetSizeClassNames: Record<DashboardWidgetSize, string> = {
  full: 'col-span-12',
  half: 'col-span-12 xl:col-span-6',
  hero: 'col-span-12',
  third: 'col-span-12 md:col-span-4',
};

const dashboardActionConfig: Record<DashboardActionSourceKey, DashboardActionConfig> = {
  users: {
    ctaLabel: 'Open users',
    href: adminUsersIndex.url(),
    permission: adminPermissions.usersView,
    variant: 'primary',
  },
  roles: {
    ctaLabel: 'Open roles',
    href: adminRolesIndex.url(),
    permission: adminPermissions.rolesView,
    variant: 'secondary',
  },
  permissions: {
    ctaLabel: 'Open permissions',
    href: adminPermissionsIndex.url(),
    permission: adminPermissions.permissionsView,
    variant: 'accent',
  },
};

const dashboardSourceLabels: Record<DashboardActionSourceKey, string> = {
  permissions: 'Permissions',
  roles: 'Roles',
  users: 'Users',
};

const buildResolvedWidget = (schema: DashboardWidgetSchema, component: Component, props: Record<string, unknown>): DashboardResolvedWidget => ({
  className: widgetSizeClassNames[schema.size],
  component,
  id: schema.id,
  props,
  widget: schema.widget,
});

const resolveAnchorWidget: DashboardWidgetResolver = (schema, context) => {
  if (schema.widget !== 'anchor') {
    return null;
  }

  return buildResolvedWidget(schema, DashboardAnchorWidget, {
    appearance: schema.appearance,
    description: schema.description,
    eyebrow: schema.eyebrow,
    summaryItems: [
      {
        label: 'Users',
        variant: dashboardActionConfig.users.variant,
        value: context.sources.overview.users,
      },
      {
        label: 'Roles',
        variant: dashboardActionConfig.roles.variant,
        value: context.sources.overview.roles,
      },
      {
        label: 'Permissions',
        variant: dashboardActionConfig.permissions.variant,
        value: context.sources.overview.permissions,
      },
    ],
    title: schema.title,
    variant: schema.variant,
  });
};

const resolveActionWidget: DashboardWidgetResolver = (schema, context) => {
  if (schema.widget !== 'action') {
    return null;
  }

  const actionConfig = dashboardActionConfig[schema.source];

  if (!context.can(actionConfig.permission)) {
    return null;
  }

  return buildResolvedWidget(schema, DashboardActionWidget, {
    appearance: schema.appearance,
    count: context.sources[schema.source].count,
    ctaLabel: schema.ctaLabel ?? actionConfig.ctaLabel,
    description: schema.description,
    emptyDescription: schema.emptyDescription,
    emptyTitle: schema.emptyTitle,
    eyebrow: schema.eyebrow,
    href: schema.href ?? actionConfig.href,
    title: schema.title ?? dashboardSourceLabels[schema.source],
    variant: schema.variant,
  });
};

const resolveStatWidget: DashboardWidgetResolver = (schema, context) => {
  if (schema.widget !== 'stat') {
    return null;
  }

  const actionConfig = dashboardActionConfig[schema.source];

  if (!context.can(actionConfig.permission)) {
    return null;
  }

  return buildResolvedWidget(schema, DashboardStatWidget, {
    appearance: schema.appearance,
    count: context.sources[schema.source].count,
    description: schema.description,
    eyebrow: schema.eyebrow,
    title: schema.title ?? dashboardSourceLabels[schema.source],
    variant: schema.variant,
  });
};

const resolveSupportWidget: DashboardWidgetResolver = (schema) => {
  if (schema.widget !== 'support') {
    return null;
  }

  return buildResolvedWidget(schema, DashboardSupportWidget, {
    appearance: schema.appearance,
    description: schema.description,
    eyebrow: schema.eyebrow,
    items: schema.items,
    title: schema.title,
    variant: schema.variant,
  });
};

const resolveChartWidget: DashboardWidgetResolver = (schema) => {
  if (schema.widget !== 'chart') {
    return null;
  }

  return buildResolvedWidget(schema, DashboardChartWidget, {
    appearance: schema.appearance,
    description: schema.description,
    emptyDescription: schema.emptyDescription,
    emptyTitle: schema.emptyTitle,
    points: [],
    title: schema.title,
    variant: schema.variant,
  });
};

const widgetResolvers: DashboardWidgetResolver[] = [resolveAnchorWidget, resolveActionWidget, resolveStatWidget, resolveSupportWidget, resolveChartWidget];

export const resolveDashboardWidgets = (schema: DashboardWidgetSchema[], context: DashboardResolverContext): DashboardResolvedWidget[] => {
  return schema.map((entry) => widgetResolvers.map((resolver) => resolver(entry, context)).find((result) => result !== null) ?? null).filter((entry): entry is DashboardResolvedWidget => entry !== null);
};
