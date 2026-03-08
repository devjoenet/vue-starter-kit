export const adminPermissions = {
  usersView: 'users.view',
  usersCreate: 'users.create',
  usersUpdate: 'users.update',
  usersDelete: 'users.delete',
  usersAssignRoles: 'users.assignRoles',
  rolesView: 'roles.view',
  rolesCreate: 'roles.create',
  rolesUpdate: 'roles.update',
  rolesDelete: 'roles.delete',
  rolesAssignPermissions: 'roles.assignPermissions',
  permissionsView: 'permissions.view',
  permissionsCreate: 'permissions.create',
  permissionsUpdate: 'permissions.update',
  permissionsDelete: 'permissions.delete',
} as const;

export type AdminPermission =
  (typeof adminPermissions)[keyof typeof adminPermissions];
