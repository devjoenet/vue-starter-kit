export type User = {
  id: number;
  name: string;
  email: string;
  avatar?: string | null;
  email_verified_at: string | null;
  [key: string]: unknown;
};

export type Auth = {
  user: User | null;
  roles: string[];
  permissions: string[];
};

export type TwoFactorConfigContent = {
  title: string;
  description: string;
  buttonText: string;
};
