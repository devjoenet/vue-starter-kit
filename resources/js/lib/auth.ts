import type { AuthPermissions, PermissionGrant } from '@/types/auth';

function resolvePermissionName(permission: PermissionGrant): string | null {
  if (!permission) {
    return null;
  }

  if (typeof permission === 'string') {
    const normalizedPermission = permission.trim();

    return normalizedPermission === '' ? null : normalizedPermission;
  }

  const namedPermission = permission.name?.trim() ?? permission.permission?.trim() ?? '';

  return namedPermission === '' ? null : namedPermission;
}

export function normalizePermissionNames(permissions: AuthPermissions | null | undefined): string[] {
  if (Array.isArray(permissions)) {
    return Array.from(new Set(permissions.map(resolvePermissionName).filter((permissionName): permissionName is string => permissionName !== null))).sort();
  }

  if (!permissions || typeof permissions !== 'object') {
    return [];
  }

  return Object.entries(permissions)
    .filter(([, isGranted]) => isGranted === true)
    .map(([permissionName]) => permissionName)
    .sort();
}
