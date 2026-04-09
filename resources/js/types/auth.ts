import type { AuthenticatedUserData, SharedAuthData } from './wayfinder-generated';

export type User = AuthenticatedUserData & {
  avatar?: string | null;
  [key: string]: unknown;
};

export type NamedPermissionGrant = {
  name?: string | null;
  permission?: string | null;
};

export type PermissionGrant = string | NamedPermissionGrant | null | undefined;
export type AuthPermissions = PermissionGrant[] | Record<string, boolean>;

export type Auth = Omit<SharedAuthData, 'user'> & {
  user: User | null;
  roles: string[];
  permissions: AuthPermissions;
};

export type TwoFactorConfigContent = {
  title: string;
  description: string;
  buttonText: string;
};
