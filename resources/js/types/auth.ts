import type { AuthenticatedUserData, SharedAuthData } from './wayfinder-generated';

export type User = AuthenticatedUserData & {
  avatar?: string | null;
  [key: string]: unknown;
};

export type Auth = Omit<SharedAuthData, 'user'> & {
  user: User | null;
  roles: string[];
  permissions: string[];
};

export type TwoFactorConfigContent = {
  title: string;
  description: string;
  buttonText: string;
};
