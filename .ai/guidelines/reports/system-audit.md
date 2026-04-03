# System Audit

Date: `2026-04-03`
Scope: `app/**`, `resources/js/**`, `resources/css/**`, `routes/**`, `tests/**`, `composer.json`, `package.json`
Audit owner: `platform`

## Executive Summary

- Open critical findings: `0`
- Open high findings: `1`
- Open medium findings: `1`
- Open low findings: `0`
- Resolved during Phase 8 follow-through: `2`
- Resolved during Phase 9 follow-through: `4`
- Reduced during Phase 8 follow-through: `1`
- Verdict: the starter is demo-safe and broadly well-covered. Backend roadmap parity, mutation traceability, request-level authorization intent, admin query indexing, and new Phase 11 observability and budget guardrails are now in place, so the remaining real work has narrowed to the frontend/dashboard follow-through, deeper accessibility, and the still-missing CI/release-governance layer.

## Positive Baseline Signals

- Admin writes are consistently permission-gated at the route layer in [routes/permissions.php](routes/permissions.php).
- Auth, settings, and two-factor flows already have meaningful feature and browser coverage across `tests/Feature/**` and `tests/Browser/**`.
- Admin index reads are generally shaped deliberately with selected columns, eager loading, and SQL-backed sorting in `app/Modules/Users/Actions/GetUserIndexItems.php`, `app/Modules/Roles/Actions/GetRoleIndexItems.php`, and `app/Modules/Permissions/Actions/GetPermissionIndexItems.php`.
- Frontend maintenance guardrails already exist for several shell, auth, and component conventions in `tests/Unit/FrontendMaintenanceGuardrailsTest.php`.

## Severity Ledger

| ID | Severity | Owner | Target phase | Area | Finding | Evidence | Status |
| --- | --- | --- | --- | --- | --- | --- | --- |
| A-001 | High | backend | Phase 9 | Backend architecture conformance | The codebase does not currently match several “completed” structural claims in the development plan, which makes the roadmap unreliable as an architectural control. | Closed by reconciling `development-plan.md` with the live flat module roots under `app/Modules/{Users,Roles,Permissions,Settings,Dashboard,Shared,Audit}` and the existing `app/Http/Controllers/{Admin,Settings}` plus `app/Http/Requests/{Admin,Settings}` transport layout. | Closed |
| A-002 | High | backend | Phase 9 | Security and compliance posture | Sensitive admin mutations do not have an explicit audit trail or traceability layer for role, permission, or destructive user changes. | Closed by `app/Modules/Audit/**`, the new `audit_logs` migration, post-commit audit writes inside the sensitive admin and settings actions, and representative feature coverage in `tests/Feature/Admin/AdminAuditLogTest.php` and `tests/Feature/Settings/SettingsAuditLogTest.php`. | Closed |
| A-003 | High | platform | Phase 12 | Tooling and delivery integrity | Verification is defined locally, but CI parity is still missing, so release discipline still depends on whoever remembers to run the commands that day. A flawless system. | The workspace does not currently include a `.github/` workflow directory, while the local release path already expects backend, frontend, and browser checks. | Open |
| A-004 | Medium | backend | Phase 9 | Data and query efficiency | Admin filter and sort paths rely on columns that are not backed by obvious follow-up indexes for the current read patterns. | Closed by `database/migrations/2026_04_03_014922_add_admin_index_support_indexes_to_acl_tables.php`, which adds dedicated non-unique indexes for the current soft-delete-aware sort and filter paths on `users`, `roles`, `permissions`, and `permission_groups`. | Closed |
| A-005 | Medium | frontend | Phase 10 | UX coherence and branding fidelity | The admin dashboard has drifted away from the `Welcome.vue` composition baseline and is still one stacked-panel refactor away from generic admin furniture. | Compare `resources/js/pages/Welcome.vue` with `resources/js/pages/admin/Dashboard.vue` and `resources/js/components/admin/AdminQuickLinks.vue`. The welcome page uses one dominant shell plus one lower supporting band; the dashboard still distributes emphasis across multiple near-peer panels and metric-like surfaces. | Open |
| A-006 | Medium | platform | Phase 8 | Tooling and delivery integrity | The Pest harness carried starter defaults and global `RefreshDatabase`, which worked but was noisier and heavier than the documented target baseline. | Resolved in `tests/Pest.php` by removing starter scaffolding noise and switching the shared feature/browser baseline to `LazilyRefreshDatabase`. | Closed |
| A-007 | Medium | frontend | Phase 8 / Phase 10 | Frontend UI quality | Browser and unit coverage validated JavaScript safety and markup conventions, but the accessibility assertion layer was too thin. | Reduced by new landmark and dashboard-hierarchy guardrails in `tests/Unit/FrontendMaintenanceGuardrailsTest.php` plus semantic `aria-labelledby` improvements on high-identity surfaces. Full keyboard/focus regression coverage still belongs to Phase 10. | Reduced |
| A-008 | Low | backend | Phase 9 | Security and compliance posture | Authorization intent is split between route middleware and permissive form requests, which weakens local traceability of access rules. | Closed by explicit ability checks in the admin form requests and explicit authenticated-user or feature guards in the settings requests, with architecture coverage in `tests/Unit/BackendArchitectureContractsTest.php`. | Closed |
| A-009 | Low | platform | Phase 8 | Tooling and delivery integrity | Starter example tests were still in the suite and diluted the baseline signal. | Resolved by deleting `tests/Feature/ExampleTest.php` and `tests/Unit/ExampleTest.php`. | Closed |

## Cross-Reference Matrix

| ID | Owner | Phase | Notes |
| --- | --- | --- | --- |
| A-001 | backend | Phase 9 | Closed by updating the development plan to the live flat module and transport layout. |
| A-002 | backend | Phase 9 | Closed by the new audit slice, migration, write-path instrumentation, and feature coverage. |
| A-003 | platform | Phase 12 | Still open until the workspace actually has CI parity for the documented local release path. |
| A-004 | backend | Phase 9 | Closed by the dedicated admin index-support migration for the current hot sort and filter paths. |
| A-005 | frontend | Phase 10 | Redesign the dashboard landing composition to follow the welcome-page shell model and add guardrails that keep it there. |
| A-006 | platform | Phase 8 | Closed via `tests/Pest.php` baseline cleanup and `LazilyRefreshDatabase` adoption. |
| A-007 | frontend | Phase 10 | Landmark coverage is now in place; full keyboard and focus regression coverage remains. |
| A-008 | backend | Phase 9 | Closed by co-locating authorization intent inside the admin and settings form requests. |
| A-009 | platform | Phase 8 | Closed via example test removal. |

## Known-Good Baseline

Verification run date: `2026-04-03`

- Backend gates:
  - `composer run style:fix` -> passed
  - `composer run phpstan` -> passed, `0` errors
  - `composer run rector` -> passed, no required refactors reported
  - `composer run test:type-coverage` -> passed, `100.0%`
  - `composer run test:parallel` -> passed, `400` tests and `2751` assertions across `10` processes
- Guidelines maintenance:
  - `php artisan boost:update` -> passed

This is the dated regression baseline after the Phase 9 hardening pass. Future follow-up work should be compared against this run instead of ad hoc local memory, which has never been a reliable storage engine.

## Phase 8 Follow-Through

The following lightweight items from later phases were pulled into Phase 8 because they were mostly guardrail or pipeline work rather than large implementation:

- cleaned the shared Pest baseline in `tests/Pest.php`
- removed starter example tests that diluted the suite signal
- added semantic-landmark and dashboard-hierarchy guardrails in `tests/Unit/FrontendMaintenanceGuardrailsTest.php`
- added targeted `aria-labelledby` improvements to the welcome, auth, and admin dashboard surfaces

## Phase 11 Follow-Through

- moved the remaining PHP validation and transformer helpers out of `app/Concerns` and `app/Support` into flat module-owned `Actions` namespaces
- added global request IDs via `X-Request-Id` plus shared Inertia request context for user-visible error references
- added slow-query threshold logging and structured performance budgets for backend query counts and high-identity first-load responses
- added a build-time frontend asset budget check that now runs from the standard `npm run build` path
- centralized stale-session, network, and partial-backend failure copy in the shared frontend request-failure helper

## Phase 9 Follow-Through

- reconciled `.ai/guidelines/development-plan.md` with the live flat module and transport namespaces
- added the `app/Modules/Audit` slice plus the `audit_logs` table for durable operator trace records
- instrumented sensitive admin and settings writes with transactional post-commit audit logging
- added explicit request-level ability and authentication guards to the admin and settings form requests
- added non-unique index support for the current admin filter and sort paths
- extended architecture and feature coverage for audited write paths and request-level authorization intent

## Dashboard Vs Welcome Comparison Note

### Structural Drift

- `Welcome.vue` leads with one dominant shell, one integrated media stage, and one lower supporting proof band.
- `admin/Dashboard.vue` opens with a strong primary shell, but then immediately splits attention across `AdminQuickLinks`, an operational overview panel, a focus panel, and a readiness panel.
- `AdminQuickLinks.vue` is cleaner than a generic metric grid, but it still behaves like a three-card admin strip with count callouts, which pulls the page back toward equal-weight dashboard furniture.

### Reusable Primitives To Keep

- The shared shell vocabulary already works: `surface-dashboard-shell`, `surface-dashboard-primary`, `surface-dashboard-secondary`, and the existing motion/tint language are usable foundations.
- The welcome page’s “dominant shell + supporting band” pattern can transfer directly to admin.
- The current quick-link strip can survive if it stays subordinate to one clear operational message instead of competing with it.

### Missing Guardrails

- The dashboard landing page needs a dedicated maintenance test that rejects:
  - stacked equal-weight metric-card layouts
  - more than one primary headline region
  - filler status panels that do not map to actual admin workflows
- The design baseline in `Welcome.vue` should be referenced directly in the future dashboard guardrail test so the landing page does not quietly become a second design language.

## Recommended Order

1. Phase 10: redesign the admin dashboard landing composition to follow the `Welcome.vue` dominant-shell baseline.
2. Phase 10: add dedicated keyboard and focus regression coverage on the high-identity auth, admin, and settings surfaces.
