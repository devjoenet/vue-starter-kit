# System Audit

Date: `2026-04-02`
Scope: `app/**`, `resources/js/**`, `resources/css/**`, `routes/**`, `tests/**`, `composer.json`, `package.json`
Audit owner: `platform`

## Executive Summary

- Open critical findings: `0`
- Open high findings: `2`
- Open medium findings: `3`
- Open low findings: `1`
- Resolved during Phase 8 follow-through: `3`
- Reduced during Phase 8 follow-through: `1`
- Verdict: the starter is demo-safe and broadly well-covered. The lightweight release-governance and guardrail work is now in place, while the remaining real work is the backend/security/indexing/dashboard substance that never belonged in a “small cleanup” bucket anyway.

## Positive Baseline Signals

- Admin writes are consistently permission-gated at the route layer in [routes/permissions.php](routes/permissions.php).
- Auth, settings, and two-factor flows already have meaningful feature and browser coverage across `tests/Feature/**` and `tests/Browser/**`.
- Admin index reads are generally shaped deliberately with selected columns, eager loading, and SQL-backed sorting in `app/Modules/Users/Actions/GetUserIndexItems.php`, `app/Modules/Roles/Actions/GetRoleIndexItems.php`, and `app/Modules/Permissions/Actions/GetPermissionIndexItems.php`.
- Frontend maintenance guardrails already exist for several shell, auth, and component conventions in `tests/Unit/FrontendMaintenanceGuardrailsTest.php`.

## Severity Ledger

| ID | Severity | Owner | Target phase | Area | Finding | Evidence | Status |
| --- | --- | --- | --- | --- | --- | --- | --- |
| A-001 | High | backend | Phase 9 | Backend architecture conformance | The codebase does not currently match several “completed” structural claims in the development plan, which makes the roadmap unreliable as an architectural control. | `app/Modules/{Users,Roles,Permissions,Settings,Dashboard,Shared}` and `app/Http/Controllers/{Admin,Settings}` remain the live structure while `development-plan.md` claims slice paths such as `app/Modules/Admin/**` and `app/Http/Admin/**`. | Open |
| A-002 | High | backend | Phase 9 | Security and compliance posture | Sensitive admin mutations do not have an explicit audit trail or traceability layer for role, permission, or destructive user changes. | No audit/activity package, audit model, or mutation logging surface appears under `app/**`; write flows such as `CreateRole`, `UpdatePermission`, `DeleteUser`, and `SyncRolePermissions` mutate state without durable operator trace records. | Open |
| A-003 | High | platform | Phase 8 | Tooling and delivery integrity | Verification was defined locally but not enforced in CI, so release parity depended on whoever remembered to run the commands that day. A flawless system. | Resolved by `.github/workflows/ci.yml`, which now runs backend quality, frontend quality, and browser smoke tiers. | Closed |
| A-004 | Medium | backend | Phase 9 | Data and query efficiency | Admin filter and sort paths rely on columns that are not backed by obvious follow-up indexes for the current read patterns. | Current migrations index unique identity fields and pivot keys, but admin queries sort/filter on `users.email`, `roles.name`, `permissions.label`, `permissions.name`, and `permission_groups.label` in `Get*IndexItems` without dedicated non-unique index review notes or follow-up migrations. | Open |
| A-005 | Medium | frontend | Phase 10 | UX coherence and branding fidelity | The admin dashboard has drifted away from the `Welcome.vue` composition baseline and is still one stacked-panel refactor away from generic admin furniture. | Compare `resources/js/pages/Welcome.vue` with `resources/js/pages/admin/Dashboard.vue` and `resources/js/components/admin/AdminQuickLinks.vue`. The welcome page uses one dominant shell plus one lower supporting band; the dashboard still distributes emphasis across multiple near-peer panels and metric-like surfaces. | Open |
| A-006 | Medium | platform | Phase 8 | Tooling and delivery integrity | The Pest harness carried starter defaults and global `RefreshDatabase`, which worked but was noisier and heavier than the documented target baseline. | Resolved in `tests/Pest.php` by removing starter scaffolding noise and switching the shared feature/browser baseline to `LazilyRefreshDatabase`. | Closed |
| A-007 | Medium | frontend | Phase 8 / Phase 10 | Frontend UI quality | Browser and unit coverage validated JavaScript safety and markup conventions, but the accessibility assertion layer was too thin. | Reduced by new landmark and dashboard-hierarchy guardrails in `tests/Unit/FrontendMaintenanceGuardrailsTest.php` plus semantic `aria-labelledby` improvements on high-identity surfaces. Full keyboard/focus regression coverage still belongs to Phase 10. | Reduced |
| A-008 | Low | backend | Phase 9 | Security and compliance posture | Authorization intent is split between route middleware and permissive form requests, which weakens local traceability of access rules. | Many admin form requests return `true` from `authorize()` while routes in `routes/permissions.php` carry the actual enforcement. This is consistent, but it hides intent away from the transport objects themselves. | Open |
| A-009 | Low | platform | Phase 8 | Tooling and delivery integrity | Starter example tests were still in the suite and diluted the baseline signal. | Resolved by deleting `tests/Feature/ExampleTest.php` and `tests/Unit/ExampleTest.php`. | Closed |

## Cross-Reference Matrix

| ID | Owner | Phase | Notes |
| --- | --- | --- | --- |
| A-001 | backend | Phase 9 | Reconcile the documented target structure with the actual namespaces and file layout before the plan becomes misleading institutional memory. |
| A-002 | backend | Phase 9 | Add explicit audit records or event-backed mutation logging for sensitive admin writes. |
| A-003 | platform | Phase 8 | Closed via `.github/workflows/ci.yml`. |
| A-004 | backend | Phase 9 | Review filter/sort indexes against the current query shapes and add migrations only where the path is truly hot. |
| A-005 | frontend | Phase 10 | Redesign the dashboard landing composition to follow the welcome-page shell model and add guardrails that keep it there. |
| A-006 | platform | Phase 8 | Closed via `tests/Pest.php` baseline cleanup and `LazilyRefreshDatabase` adoption. |
| A-007 | frontend | Phase 10 | Landmark coverage is now in place; full keyboard and focus regression coverage remains. |
| A-008 | backend | Phase 9 | Decide whether request-level authorization should remain intentionally permissive or become co-located with the form requests. |
| A-009 | platform | Phase 8 | Closed via example test removal. |

## Known-Good Baseline

Verification run date: `2026-04-02`

- Backend gates:
  - `composer run style:fix` -> passed
  - `composer run phpstan` -> passed, `0` errors
  - `composer run rector` -> passed, no required refactors reported
  - `composer run test:type-coverage` -> passed, `100.0%`
  - `composer run test:parallel` -> passed, `369` tests and `2634` assertions across `10` processes
- Frontend gates:
  - `npm run test:frontend` -> passed
  - `npm run build` -> passed
- Guidelines maintenance:
  - `php artisan boost:update` -> passed

This is the dated regression baseline for the Phase 8 audit. Future follow-up work should be compared against this run instead of ad hoc local memory, which has never been a reliable storage engine.

## Phase 8 Follow-Through

The following lightweight items from later phases were pulled into Phase 8 because they were mostly guardrail or pipeline work rather than large implementation:

- added GitHub Actions CI parity in `.github/workflows/ci.yml`
- cleaned the shared Pest baseline in `tests/Pest.php`
- removed starter example tests that diluted the suite signal
- added semantic-landmark and dashboard-hierarchy guardrails in `tests/Unit/FrontendMaintenanceGuardrailsTest.php`
- added targeted `aria-labelledby` improvements to the welcome, auth, and admin dashboard surfaces

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

1. Phase 9: fix the roadmap/code drift, add mutation traceability, and put CI behind the existing local gates.
2. Phase 9: review admin query indexes and normalize the project-owned Pest harness.
3. Phase 10: redesign the admin dashboard landing composition and add explicit a11y plus composition guardrails.
