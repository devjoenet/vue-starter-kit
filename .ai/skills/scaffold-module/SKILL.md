---
name: scaffold-module
description: Procedural workflow for creating new domain modules and HTTP transport layers.
---

# Scaffolding Modules
*(Standards: Refer to Guidelines: Architecture)*

This skill provides the procedural workflow for creating new package-native domain modules and their transport layers.

## 1. Procedural Steps

1. **Naming**: Identify the PascalCase module name (e.g., `Billing`).
2. **Directory Creation**: Create the base module under `Modules/{Domain}` with `app/`, `routes/`, `resources/`, and `tests/` folders as needed.
3. **Registering Routes**: Register feature routes in the module's own `routes/web.php` or `routes/api.php` through the module providers.
4. **Scaffolding Core**: Use artisan commands (e.g., `module:make`, `make:model`, `make:class`) to populate the module-local logic layer.

## 2. Examples

- **[Module Directory Tree](file:///Volumes/Dev/laravel-aion/.ai/skills/scaffold-module/examples/directory-tree.txt)**

> [!IMPORTANT]
> When scaffolding, always verify that no cross-module model usage occurs. Use Contracts (placed in `Contracts/` folder) for interaction.
