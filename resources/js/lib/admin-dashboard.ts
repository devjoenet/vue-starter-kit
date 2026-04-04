import type { Component } from 'vue';
import DashboardActionWidget from '@/components/admin/dashboard/DashboardActionWidget.vue';
import DashboardAnchorWidget from '@/components/admin/dashboard/DashboardAnchorWidget.vue';
import DashboardChartWidget from '@/components/admin/dashboard/DashboardChartWidget.vue';
import DashboardStatWidget from '@/components/admin/dashboard/DashboardStatWidget.vue';
import DashboardSupportWidget from '@/components/admin/dashboard/DashboardSupportWidget.vue';
import { index as adminPermissionsIndex } from '@/routes/admin/permissions';
import { index as adminRolesIndex } from '@/routes/admin/roles';
import { index as adminUsersIndex } from '@/routes/admin/users';
import { adminPermissions } from '@/types/admin-permissions';
import type { AdminDashboardSources, DashboardActionSourceKey, DashboardResolvedWidget, DashboardWidgetSchema, DashboardWidgetSize, DashboardWidgetTone } from '@/types/admin/dashboard';

type DashboardResolverContext = {
  can: (permission: string) => boolean;
  sources: AdminDashboardSources;
};

type DashboardWidgetResolver = (schema: DashboardWidgetSchema, context: DashboardResolverContext) => DashboardResolvedWidget | null;

type DashboardActionConfig = {
  ctaLabel: string;
  href: string;
  permission: string;
  tone: DashboardWidgetTone;
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
    tone: 'users',
  },
  roles: {
    ctaLabel: 'Open roles',
    href: adminRolesIndex.url(),
    permission: adminPermissions.rolesView,
    tone: 'roles',
  },
  permissions: {
    ctaLabel: 'Open permissions',
    href: adminPermissionsIndex.url(),
    permission: adminPermissions.permissionsView,
    tone: 'permissions',
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

  const availableActions = (Object.keys(dashboardActionConfig) as DashboardActionSourceKey[])
    .filter((key) => context.can(dashboardActionConfig[key].permission))
    .map((key) => ({
      href: dashboardActionConfig[key].href,
      label: dashboardActionConfig[key].ctaLabel,
      tone: dashboardActionConfig[key].tone,
    }));

  return buildResolvedWidget(schema, DashboardAnchorWidget, {
    description: schema.description,
    emptyDescription: 'This account does not have any admin surfaces assigned yet. Keep the board in place and the actionable cards will appear when access is granted.',
    eyebrow: schema.eyebrow,
    points: schema.points,
    primaryAction: availableActions[0] ?? null,
    summaryItems: [
      {
        label: 'Users',
        tone: 'users',
        value: context.sources.overview.users,
      },
      {
        label: 'Roles',
        tone: 'roles',
        value: context.sources.overview.roles,
      },
      {
        label: 'Permissions',
        tone: 'permissions',
        value: context.sources.overview.permissions,
      },
    ],
    title: schema.title,
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
    count: context.sources[schema.source].count,
    ctaLabel: schema.ctaLabel ?? actionConfig.ctaLabel,
    description: schema.description,
    emptyDescription: schema.emptyDescription,
    emptyTitle: schema.emptyTitle,
    eyebrow: schema.eyebrow,
    href: schema.href ?? actionConfig.href,
    title: schema.title ?? dashboardSourceLabels[schema.source],
    tone: schema.tone ?? actionConfig.tone,
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
    count: context.sources[schema.source].count,
    description: schema.description,
    eyebrow: schema.eyebrow,
    title: schema.title ?? dashboardSourceLabels[schema.source],
    tone: schema.tone ?? actionConfig.tone,
  });
};

const resolveSupportWidget: DashboardWidgetResolver = (schema) => {
  if (schema.widget !== 'support') {
    return null;
  }

  return buildResolvedWidget(schema, DashboardSupportWidget, {
    description: schema.description,
    eyebrow: schema.eyebrow,
    items: schema.items,
    title: schema.title,
  });
};

const resolveChartWidget: DashboardWidgetResolver = (schema) => {
  if (schema.widget !== 'chart') {
    return null;
  }

  return buildResolvedWidget(schema, DashboardChartWidget, {
    description: schema.description,
    emptyDescription: schema.emptyDescription,
    emptyTitle: schema.emptyTitle,
    points: [],
    title: schema.title,
    tone: schema.tone ?? 'neutral',
  });
};

const widgetResolvers: DashboardWidgetResolver[] = [resolveAnchorWidget, resolveActionWidget, resolveStatWidget, resolveSupportWidget, resolveChartWidget];

export const adminDashboardBoardSchema: DashboardWidgetSchema[] = [
  {
    description: 'Keep the dashboard lean on purpose: lead with users, roles, and permissions now, then add richer widgets only when the board has real signals worth showing.',
    eyebrow: 'Global board',
    id: 'anchor',
    points: [
      'Use users when you need to confirm who can reach the workspace right now.',
      'Use roles to keep reusable access language steady across demos and real builds.',
      'Use permissions when a workflow needs precise gates instead of broad access.',
    ],
    size: 'hero',
    source: 'overview',
    title: 'Open the board, then move straight into access work.',
    widget: 'anchor',
  },
  {
    ctaLabel: 'Open users',
    description: 'Review the people who can currently reach the workspace and adjust them without wading through filler.',
    emptyDescription: 'No user accounts exist yet. Add the first account before the board tries to tell a bigger story.',
    emptyTitle: 'No user accounts live yet.',
    eyebrow: 'Action surface',
    id: 'users-action',
    size: 'third',
    source: 'users',
    title: 'Users',
    widget: 'action',
  },
  {
    ctaLabel: 'Open roles',
    description: 'Keep reusable policies clear so demos and operational builds can speak the same access language.',
    emptyDescription: 'No roles exist yet. Define the reusable access groups before the board grows more complex than the ACL itself.',
    emptyTitle: 'No roles live yet.',
    eyebrow: 'Action surface',
    id: 'roles-action',
    size: 'third',
    source: 'roles',
    title: 'Roles',
    widget: 'action',
  },
  {
    ctaLabel: 'Open permissions',
    description: 'Use precise permission checks when workflows need guardrails tighter than a broad role can provide.',
    emptyDescription: 'No permission checks exist yet. Add the workflow gates first and leave the decorative graphing for later.',
    emptyTitle: 'No permissions live yet.',
    eyebrow: 'Action surface',
    id: 'permissions-action',
    size: 'third',
    source: 'permissions',
    title: 'Permissions',
    widget: 'action',
  },
  {
    description: 'Accounts currently wired into the workspace.',
    eyebrow: 'Snapshot',
    id: 'users-stat',
    size: 'third',
    source: 'users',
    title: 'Users',
    widget: 'stat',
  },
  {
    description: 'Reusable access groups ready to be assigned.',
    eyebrow: 'Snapshot',
    id: 'roles-stat',
    size: 'third',
    source: 'roles',
    title: 'Roles',
    widget: 'stat',
  },
  {
    description: 'Workflow gates already available to wire in.',
    eyebrow: 'Snapshot',
    id: 'permissions-stat',
    size: 'third',
    source: 'permissions',
    title: 'Permissions',
    widget: 'stat',
  },
  {
    description: 'This board is the launch surface for real work, not a dumping ground for random dashboard furniture.',
    eyebrow: 'Keep it honest',
    id: 'support',
    items: [
      'Add a widget only when it either starts work faster or explains a real system state cleanly.',
      'Keep domain color assignments consistent so the board can scale without looking like a sticker wall.',
      'Prefer empty-state guidance over fake activity, and keep charts off the board until they have honest data.',
    ],
    size: 'full',
    title: 'Grow the board deliberately, not decoratively.',
    widget: 'support',
  },
];

export const resolveDashboardWidgets = (schema: DashboardWidgetSchema[], context: DashboardResolverContext): DashboardResolvedWidget[] => {
  return schema.map((entry) => widgetResolvers.map((resolver) => resolver(entry, context)).find((result) => result !== null) ?? null).filter((entry): entry is DashboardResolvedWidget => entry !== null);
};
