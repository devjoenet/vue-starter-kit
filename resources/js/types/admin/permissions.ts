import type { PermissionGroupOptionData, PermissionIndexFilterOptionsData, PermissionIndexItemData, PermissionItemData } from '@/types/wayfinder-generated';
import type { AuditHistoryItem } from '@/types/admin/audit';
import type { AdminIndexQuery } from '@/types/admin/shared';

export type PermissionGroupOption = Omit<PermissionGroupOptionData, 'description'> & {
  description?: string | null;
};

export type PermissionItem = Omit<PermissionItemData, 'description' | 'group_description'> & {
  description?: string | null;
  group_description?: string | null;
};

export type PermissionIndexItem = Omit<PermissionIndexItemData, 'description' | 'group_description'> & {
  description?: string | null;
  group_description?: string | null;
};
export type AdminPermissionsIndexFilterOptions = PermissionIndexFilterOptionsData & {
  group: string[];
  permission: string[];
  permission_check: string[];
};

export type AdminPermissionsIndexColumn = 'id' | 'group' | 'permission' | 'permission_check';

export type AdminPermissionsIndexPageProps = {
  permissions: PermissionIndexItem[];
  groups: PermissionGroupOption[];
  filterOptions: AdminPermissionsIndexFilterOptions;
  query: AdminIndexQuery<AdminPermissionsIndexColumn>;
};

export type AdminPermissionsCreatePageProps = {
  groups: PermissionGroupOption[];
};

export type AdminPermissionsEditPageProps = {
  permission: PermissionItem;
  auditHistory?: AuditHistoryItem[];
  groups: PermissionGroupOption[];
};
