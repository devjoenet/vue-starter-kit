import { toCamelCase, toSnakeCase } from '@/lib/utils';

export function prefixPermissionWithGroup(
  group: string,
  actionSegment = '',
): string {
  const normalizedGroup = toSnakeCase(group);
  const normalizedAction = toCamelCase(actionSegment);

  return normalizedAction
    ? `${normalizedGroup}.${normalizedAction}`
    : `${normalizedGroup}.`;
}

export function extractPermissionActionSegment(
  permissionName: string,
  group: string,
): string {
  const normalizedGroup = toSnakeCase(group);
  const rawValue = permissionName.trim();

  if (!rawValue) {
    return '';
  }

  if (rawValue.startsWith(`${normalizedGroup}.`)) {
    return rawValue.slice(normalizedGroup.length + 1);
  }

  const segments = rawValue
    .split('.')
    .map((segment) => segment.trim())
    .filter(Boolean);

  if (segments.length > 1) {
    return segments[segments.length - 1];
  }

  return rawValue;
}

export function normalizePermissionName(
  permissionName: string,
  group: string,
): string {
  const actionSegment = extractPermissionActionSegment(permissionName, group);

  return prefixPermissionWithGroup(group, actionSegment);
}
