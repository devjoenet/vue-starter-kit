import type { AppPageProps } from './app-page-props';

export type PageProps<
  T extends Record<string, unknown> = Record<string, never>,
> = AppPageProps<T>;

export type AdminDashboardCounts = {
  users: number;
  roles: number;
  permissions: number;
};

export type UserListItem = {
  id: number;
  name: string;
  email: string;
  roles: string[];
};

export type EditableUser = {
  id: number;
  name: string;
  email: string;
};

export type RoleOption = {
  id: number;
  name: string;
};

export type RoleListItem = {
  id: number;
  name: string;
  users_count: number;
};

export type AssignableUser = {
  id: number;
  name: string;
  email: string;
};

export type EditableRole = {
  id: number;
  name: string;
};

export type PermissionItem = {
  id: number;
  name: string;
  group: string;
};

export type PermissionsByGroup = Record<string, PermissionItem[]>;

export type PaginatedCollection<T> = {
  data: T[];
} & Record<string, unknown>;

export type AdminDashboardPageProps = PageProps<{
  counts: AdminDashboardCounts;
}>;

export type AdminUsersIndexPageProps = PageProps<{
  users: PaginatedCollection<UserListItem>;
}>;

export type AdminUsersCreatePageProps = PageProps;

export type AdminUsersEditPageProps = PageProps<{
  user: EditableUser;
  roles: RoleOption[];
  userRoles: string[];
}>;

export type AdminRolesIndexPageProps = PageProps<{
  roles: RoleListItem[];
}>;

export type AdminRolesCreatePageProps = PageProps<{
  users: AssignableUser[];
}>;

export type AdminRolesEditPageProps = PageProps<{
  role: EditableRole;
  permissionsByGroup: PermissionsByGroup;
  rolePermissions: string[];
}>;

export type AdminPermissionsIndexPageProps = PageProps<{
  permissionsByGroup: PermissionsByGroup;
}>;

export type AdminPermissionsCreatePageProps = PageProps<{
  groups: string[];
}>;

export type AdminPermissionsEditPageProps = PageProps<{
  permission: PermissionItem;
  groups: string[];
}>;

export type SettingsProfilePageProps = PageProps<{
  mustVerifyEmail: boolean;
  status?: string;
}>;

export type SettingsTwoFactorPageProps = PageProps<{
  requiresConfirmation?: boolean;
  twoFactorEnabled?: boolean;
}>;
