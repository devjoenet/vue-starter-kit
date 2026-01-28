# Spatie Laravel Permission usage (project standard)

## Goal

We use Spatie Permission for admin authorization: roles, permissions, and permission checks in:

- Controllers (authorize/can middleware)
- Inertia shared props (to drive nav visibility)
- Vue components (disable/hide UI by permission)
- Pest tests

## Naming convention

Permissions use a strict "group.action" format:

- users.view, users.create, users.update, users.delete, users.assignRoles
- roles.view, roles.create, roles.update, roles.delete, roles.assignPermissions
- permissions.view, permissions.create, permissions.update, permissions.delete

Additionally, each Permission row has a `group` column:

- group: users | roles | permissions
- name: group.action (string)

## Server-side rules

- All admin routes live under /admin
- Every route has explicit `can:` middleware
- Controllers also call `$this->authorize(...)` as defense in depth
- User model uses `HasRoles`

## Client-side rules

- Nav items only render if the user has the required permission
- Forms are disabled if lacking create/update permissions
- Submit handlers should be blocked if not allowed
- Always use the server as source of truth (UI checks are convenience)

## Boost integration

When laravel/boost generates models/controllers/pages:

- Ensure controllers include `->middleware('can:...')` at the route level
- Ensure tests exist for each permission gate
- Ensure seeders create all permissions + super-admin role
