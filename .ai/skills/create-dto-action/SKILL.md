---
name: create-dto-action
description: Procedural workflow and templates for creating strict DTOs and Single Action Classes.
---

# Creating DTOs and Actions
*(Standards: Refer to Guidelines: Architecture)*

This skill provides the procedural workflow and templates for creating DTOs and Actions.

## 1. Procedural Steps

1. **Naming**: Identify a clear action name that matches the established slice convention (e.g., `CreateUser`, `UpdateRole`, `ProcessOrder`).
2. **Provisioning**: Create a DTO that encapsulates ALL inputs required by the Action. If the Action needs a Model, the DTO factory (e.g. `fromRequest`) is responsible for resolving it. As a general rule, the action should not be performing the actual query unless the query being elsewhere would add too much complexity.
3. **Scaffolding**: Use `php artisan make:class` in the directory that matches the slice you are working in. Prefer `Modules/{Domain}/app/Actions/{Name}` for package-native modular work, but follow the existing location when extending a legacy slice that has not been migrated yet.
4. **Implementation**: Every action must expose a public static `handle()` method. Private helper methods are encouraged when they simplify the main path. Additional callable methods are allowed when they genuinely improve clarity or reuse, but they must also be `public static`.
5. **DTO Shape**: Use a DTO when it makes grouped inputs easier to reason about. `final`, `readonly`, and mutable DTOs are all acceptable when applied intentionally.
6. **Finalization**: Re-read sibling files and make sure the action/DTO shape matches the local convention of the slice you touched.

## 2. Examples

> [!TIP]
> Use `php artisan make:class ...` for Actions and DTOs so the generated class starts in the correct namespace, then move it only if the slice's existing structure requires it.
