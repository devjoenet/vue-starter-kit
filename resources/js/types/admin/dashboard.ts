export type AdminDashboardCounts = {
  users: number;
  roles: number;
  permissions: number;
};

export type AdminDashboardPageProps = {
  counts: AdminDashboardCounts;
};
