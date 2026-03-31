import type { AssignableUserData, EditableRoleData, PermissionItemData, RoleIndexFilterOptionsData, RoleListItemData } from '@/types/wayfinder-generated';
import type { AdminIndexQuery } from '@/types/admin/shared';

export type RoleListItem = RoleListItemData;
export type AssignableUser = AssignableUserData;
export type EditableRole = EditableRoleData;
export type PermissionItem = PermissionItemData;
export type PermissionsByGroup = Record<string, PermissionItem[]>;
export type AdminRolesIndexFilterOptions = RoleIndexFilterOptionsData & {
  display_name: string[];
  slug: string[];
  users: string[];
};

export type AdminRolesIndexColumn = 'id' | 'display_name' | 'slug' | 'users';

export type AdminRolesIndexPageProps = {
  roles: RoleListItem[];
  filterOptions: AdminRolesIndexFilterOptions;
  query: AdminIndexQuery<AdminRolesIndexColumn>;
};

export type AdminRolesCreatePageProps = {
  users: AssignableUser[];
};

export type AdminRolesEditPageProps = {
  role: EditableRole;
  permissionsByGroup?: PermissionsByGroup;
  rolePermissions: string[];
};
