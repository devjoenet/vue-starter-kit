# Spatie Laravel Data usage (project standard)

## Goal

Replace scattered request arrays with typed Data objects:

- Create/update payloads (UserUpsertData, RoleUpsertData, PermissionUpsertData)
- View models for Inertia pages (UserListData etc.)

## Rules

- Prefer Data objects over ad-hoc arrays
- Use `Data::from($request)` or `Data::validateAndCreate($request)` patterns when possible
- Keep Data objects immutable (public readonly where possible)
- Validation rules live in the Data object when practical
- Convert Eloquent models to Data for Inertia props, not vice versa

## Boost integration

When boost generates:

- Replace controller `$request->validate(...)` with Data validation
- Return `Data::collection(...)` when sending lists to the UI
