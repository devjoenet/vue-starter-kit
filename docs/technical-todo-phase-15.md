# Technical To-Do: Phase 15 Product Module Expansion

Phase goal: provide a repeatable implementation checklist for future business-domain modules without breaking the established modular architecture.

## Module Scaffolding

- Create `app/Modules/{ModuleName}` with `Actions`, `Contracts`, `DTOs`, `Events`, `Exceptions`, `Models`, and optional `Requests` only when needed.
- Add migrations using Laravel defaults unless module-local migration organization is formally adopted.
- Add factories and seeders for deterministic development and tests.
- Register module-specific services in an existing or new service provider only when dependency wiring is needed.
- Add architecture tests or update existing guardrails to enforce contract-only cross-module access.

## Backend Resource Implementation

- Create Eloquent models with casts, relationships, scopes, authorization helpers, and query methods as needed.
- Create form requests for each create, update, delete, restore, bulk, import, or export operation.
- Create DTOs for request data, index query data, index item data, detail data, and option lists.
- Create single action classes for create, update, delete, restore, assign, sync, import, export, and index operations.
- Wrap multi-write actions in transactions and emit auditable domain events for sensitive mutations.
- Add policies or gates, then ensure form requests call authorization consistently.
- Add routes in the appropriate route file and generate/update Wayfinder route helpers.
- Add controllers that only authorize via requests, call actions, and return Inertia responses or redirects.

## Admin Frontend Implementation

- Create typed page props under `resources/js/types/admin/{module}.ts`.
- Create index page components under `resources/js/pages/admin` using shared admin layout conventions.
- Create focused admin surface components under `resources/js/components/admin` for tables, mobile lists, filters, detail forms, and assignment widgets.
- Use existing shared components for page headers, index table cards, editor shells, action rows, confirmation dialogs, toasts, and error display.
- Use Wayfinder-generated helpers for links, forms, and visits instead of hardcoded URLs.
- Use Inertia `<Form>` for forms unless a documented reason requires `useForm` or router visits.
- Add mobile, tablet, desktop, dark mode, keyboard, and reduced-motion behavior for each new surface.

## Audit and Dashboard Integration

- Add auditable events for create, update, delete, restore, assignment, import, export, and protected-resource changes.
- Add audit history formatters for human-readable diffs where necessary.
- Add dashboard metric providers only for metrics that help users make decisions.
- Ensure metrics are permission-scoped and query-budget compliant.
- Link dashboard cards to filtered admin index routes when relevant.

## Testing Requirements

- Add feature tests for authorization, validation, successful mutation, failed mutation, audit logging, and index query behavior.
- Add unit tests for actions, DTO transformations, model scopes, normalizers, and domain exceptions where useful.
- Add browser tests for keyboard, focus, empty state, loading state, destructive confirmation, and responsive admin flows.
- Add architecture tests if the new module introduces new boundaries or contracts.
- Add performance budget tests for expensive index pages or dashboard providers.
