export type SettingsProfilePageProps = {
  mustVerifyEmail: boolean;
  status?: string;
};

export type SettingsTwoFactorPageProps = {
  requiresConfirmation?: boolean;
  twoFactorEnabled?: boolean;
};
