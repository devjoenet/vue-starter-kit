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

export type PermissionGroupOption = {
  slug: string;
  label: string;
  description: string | null;
  permissions_count: number;
};

export type PermissionItem = {
  id: number;
  name: string;
  group: string;
  label: string;
  description: string | null;
  group_label: string;
  group_description: string | null;
};

export type PermissionIndexItem = {
  id: number;
  group: string;
  group_label: string;
  group_description: string | null;
  name: string;
  label: string;
  description: string | null;
  suffix: string;
};

export type PermissionsByGroup = Record<string, PermissionItem[]>;

export type PaginatedCollection<T> = {
  data: T[];
} & Record<string, unknown>;

export type AdminIndexDirection = 'asc' | 'desc';

export type AdminIndexQuery<TColumn extends string = string> = {
  sort: TColumn;
  direction: AdminIndexDirection;
  filters: Partial<Record<TColumn, string[]>>;
};

export type AdminUsersIndexColumn = 'id' | 'name' | 'email' | 'roles';
export type AdminRolesIndexColumn = 'id' | 'display_name' | 'slug' | 'users';
export type AdminPermissionsIndexColumn = 'id' | 'group' | 'permission' | 'permission_check';

export type AdminUsersIndexFilterOptions = {
  name: string[];
  email: string[];
  roles: string[];
};

export type AdminRolesIndexFilterOptions = {
  display_name: string[];
  slug: string[];
  users: string[];
};

export type AdminPermissionsIndexFilterOptions = {
  group: string[];
  permission: string[];
  permission_check: string[];
};

export type AdminDashboardPageProps = {
  counts: AdminDashboardCounts;
};

export type AdminUsersIndexPageProps = {
  users: PaginatedCollection<UserListItem>;
  filterOptions: AdminUsersIndexFilterOptions;
  query: AdminIndexQuery<AdminUsersIndexColumn>;
};

export type AdminUsersCreatePageProps = {};

export type AdminUsersEditPageProps = {
  user: EditableUser;
  roles: RoleOption[];
  userRoles: string[];
};

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
  permissionsByGroup: PermissionsByGroup;
  rolePermissions: string[];
};

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
  groups: PermissionGroupOption[];
};

export type SettingsProfilePageProps = {
  mustVerifyEmail: boolean;
  status?: string;
};

export type SettingsTwoFactorPageProps = {
  requiresConfirmation?: boolean;
  twoFactorEnabled?: boolean;
};
