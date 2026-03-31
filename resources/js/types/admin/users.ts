import type { EditableUserData, RoleOptionData, UserIndexFilterOptionsData, UserListItemData } from '@/types/wayfinder-generated';
import type { AdminIndexQuery, PaginatedCollection } from '@/types/admin/shared';

export type UserListItem = UserListItemData & {
  roles: string[];
};
export type EditableUser = EditableUserData;
export type RoleOption = RoleOptionData;
export type AdminUsersIndexFilterOptions = UserIndexFilterOptionsData & {
  name: string[];
  email: string[];
  roles: string[];
};

export type AdminUsersIndexColumn = 'id' | 'name' | 'email' | 'roles';

export type AdminUsersIndexPageProps = {
  users: PaginatedCollection<UserListItem>;
  filterOptions: AdminUsersIndexFilterOptions;
  query: AdminIndexQuery<AdminUsersIndexColumn>;
};

export type AdminUsersCreatePageProps = {};

export type AdminUsersEditPageProps = {
  user: EditableUser;
  roles?: RoleOption[];
  userRoles: string[];
};
