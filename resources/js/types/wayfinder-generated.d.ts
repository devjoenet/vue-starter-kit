export type AdminPermission =
  | 'users.view'
  | 'users.create'
  | 'users.update'
  | 'users.delete'
  | 'users.assignRoles'
  | 'roles.view'
  | 'roles.create'
  | 'roles.update'
  | 'roles.delete'
  | 'roles.assignPermissions'
  | 'permissions.view'
  | 'permissions.create'
  | 'permissions.update'
  | 'permissions.delete';
export type PasswordUpdateRequest = {
  current_password: string;
  password: string;
};
export type ProfileDeleteRequest = {
  password: string;
};
export type ProfileUpdateRequest = {
  name: string;
  email: string;
};
export type StorePermissionRequest = {
  name: string;
  group: string;
};
export type StoreRoleRequest = {
  name: string;
  user_ids?: Array<number>;
};
export type StoreUserRequest = {
  name: string;
  email: string;
  password: string;
  password_confirmation: string;
};
export type SyncRolePermissionsRequest = {
  permissions?: Array<string>;
};
export type SyncUserRolesRequest = {
  roles?: Array<string>;
};
export type TwoFactorAuthenticationRequest = Record<string, never>;
export type UpdatePermissionRequest = {
  name: string;
  group: string;
};
export type UpdateRoleRequest = {
  name: string;
};
export type UpdateUserRequest = {
  name: string;
  email: string;
  password?: string;
  password_confirmation?: string;
};
