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

export type AdminDashboardPageProps = {
  counts: AdminDashboardCounts;
};

export type AdminUsersIndexPageProps = {
  users: PaginatedCollection<UserListItem>;
};

export type AdminUsersCreatePageProps = {};

export type AdminUsersEditPageProps = {
  user: EditableUser;
  roles: RoleOption[];
  userRoles: string[];
};

export type AdminRolesIndexPageProps = {
  roles: RoleListItem[];
};

export type AdminRolesCreatePageProps = {
  users: AssignableUser[];
};

export type AdminRolesEditPageProps = {
  role: EditableRole;
  permissionsByGroup: PermissionsByGroup;
  rolePermissions: string[];
};

export type AdminPermissionsIndexPageProps = {
  permissionsByGroup: PermissionsByGroup;
};

export type AdminPermissionsCreatePageProps = {
  groups: string[];
};

export type AdminPermissionsEditPageProps = {
  permission: PermissionItem;
  groups: string[];
};

export type SettingsProfilePageProps = {
  mustVerifyEmail: boolean;
  status?: string;
};

export type SettingsTwoFactorPageProps = {
  requiresConfirmation?: boolean;
  twoFactorEnabled?: boolean;
};
