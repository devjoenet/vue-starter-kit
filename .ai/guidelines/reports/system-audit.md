# System Audit

Date: `2026-04-10`
Scope: `app/**`, `resources/js/**`, `resources/css/**`, `routes/**`, `tests/**`, `composer.json`, `package.json`
Audit owner: `platform`

## Executive Summary

- Open critical findings: `0`
- Open high findings: `1`
- Open medium findings: `0`
- Open low findings: `0`
- Resolved during Phase 8 follow-through: `2`
- Resolved during Phase 9 follow-through: `4`
- Resolved during Phase 12 follow-through: `1`
- Reduced during Phase 8 follow-through: `1`
- Verdict: the starter remains demo-safe and broadly well-covered. After the Phase 12 hardening pass, the remaining real work is the deferred CI/release-governance layer and deeper accessibility regression coverage. The stale dashboard-drift note has finally been removed from the evidence locker.

## Positive Baseline Signals

- Admin writes are consistently permission-gated at the route and request layers in `routes/permissions.php` plus the admin/settings request classes.
- Shared identity validation now lives in `Modules/Core/app/Actions/UserIdentityValidationRules.php`, so settings, registration, and console user creation no longer leak through an auth-local helper.
- Protected `super-admin` mutations now fail through explicit IAM validation exceptions with structured context, and permission lifecycle events keep the protected role synced to the live catalog.
- Frontend admin page layering now follows the intended shell-plus-surface split for users, roles, permissions, and audit-log filters, with maintenance tests guarding the extracted surfaces.

## Severity Ledger

| ID | Severity | Owner | Target phase | Area | Finding | Evidence | Status |
| --- | --- | --- | --- | --- | --- | --- | --- |
| A-001 | High | backend | Phase 9 | Backend architecture conformance | The codebase did not previously match several “completed” structural claims in the development plan, which made the roadmap unreliable as an architectural control. | Closed by restoring `.ai/guidelines/development-plan.md` with the live flat module and transport layout, then extending architecture tests to reject non-contract cross-module imports. | Closed |
| A-002 | High | backend | Phase 9 | Security and compliance posture | Sensitive admin mutations previously lacked an explicit audit trail or traceability layer for role, permission, or destructive user changes. | Closed by `Modules/Audit/**`, the `audit_logs` table, post-commit audit writes inside sensitive admin/settings actions, and representative feature coverage in `tests/Feature/Admin/**` and `tests/Feature/Settings/**`. | Closed |
| A-003 | High | platform | Phase 12 | Tooling and delivery integrity | Verification is defined locally, but CI parity is still missing, so release discipline still depends on whoever remembers to run the commands that day. A flawless system. | The workspace still does not include a `.github/` workflow directory, and CI remains intentionally deferred until deployment tooling and runners are confirmed. | Open |
| A-004 | Medium | backend | Phase 9 | Data and query efficiency | Admin filter and sort paths relied on columns that were not backed by obvious follow-up indexes for the current read patterns. | Closed by `database/migrations/2026_04_03_014922_add_admin_index_support_indexes_to_acl_tables.php`, which adds dedicated non-unique indexes for the current soft-delete-aware sort and filter paths. | Closed |
| A-005 | Medium | frontend | Phase 12 | UX coherence and architecture hygiene | The old dashboard-drift finding no longer matched the accepted product baseline and had become stale documentation rather than an actionable defect. | Closed by adopting the current dashboard composition as the live baseline in `.ai/guidelines/development-plan.md`, extracting the remaining page-heavy admin index and audit filter surfaces, and updating frontend guardrails to enforce the page-shell versus feature-surface split. | Closed |
| A-006 | Medium | platform | Phase 8 | Tooling and delivery integrity | The Pest harness carried starter defaults and global `RefreshDatabase`, which worked but was noisier and heavier than the documented target baseline. | Closed in `tests/Pest.php` by removing starter scaffolding noise and switching the shared feature/browser baseline to `LazilyRefreshDatabase`. | Closed |
| A-007 | Medium | frontend | Phase 13 | Frontend UI quality | Browser and unit coverage validate JavaScript safety and markup conventions, but the accessibility regression layer is still thinner than the intended target. | Reduced by landmark and admin-surface guardrails in `tests/Unit/FrontendMaintenanceGuardrailsTest.php`; dedicated keyboard and focus regression coverage still belongs to Phase 13. | Reduced |
| A-008 | Low | backend | Phase 9 | Security and compliance posture | Authorization intent was previously split between route middleware and permissive form requests, which weakened local traceability of access rules. | Closed by explicit ability checks in the admin form requests and explicit authenticated-user or feature guards in the settings requests, with architecture coverage in `tests/Unit/BackendArchitectureContractsTest.php`. | Closed |
| A-009 | Low | platform | Phase 8 | Tooling and delivery integrity | Starter example tests were still in the suite and diluted the baseline signal. | Closed by deleting `tests/Feature/ExampleTest.php` and `tests/Unit/ExampleTest.php`. | Closed |

## Cross-Reference Matrix

| ID | Owner | Phase | Notes |
| --- | --- | --- | --- |
| A-001 | backend | Phase 9 / Phase 12 | Closed by restoring the canonical roadmap and enforcing the allowed module dependency graph in architecture tests. |
| A-002 | backend | Phase 9 | Closed by the audit slice, migration, transactional instrumentation, and feature coverage. |
| A-003 | platform | Deferred | Still open until the workspace has CI parity for the documented local release path. |
| A-004 | backend | Phase 9 | Closed by the dedicated admin index-support migration for the current hot sort and filter paths. |
| A-005 | frontend | Phase 12 | Closed by retiring the stale dashboard-drift note and standardizing admin page-to-surface composition guardrails. |
| A-006 | platform | Phase 8 | Closed via Pest baseline cleanup and `LazilyRefreshDatabase` adoption. |
| A-007 | frontend | Phase 13 | Landmark coverage is in place; keyboard and focus regression coverage remains. |
| A-008 | backend | Phase 9 | Closed by co-locating authorization intent inside the admin and settings form requests. |
| A-009 | platform | Phase 8 | Closed via example test removal. |

## Known-Good Baseline

Verification run date: `2026-04-10`

- Backend gates:
  - `composer run style:fix` -> passed
  - `composer run phpstan` -> passed
  - `composer run rector` -> passed
  - `composer run test:type-coverage` -> passed
  - `composer run test:parallel` -> passed
- Frontend gates:
  - `npm run test:frontend` -> passed
  - `npm run build` -> passed
- Guidelines maintenance:
  - `php artisan boost:update` -> passed

This is the current regression baseline after the Phase 12 hardening pass. Future work should compare against this snapshot instead of relying on memory, which continues to be a very poor database engine.

## Phase 8 Follow-Through

- cleaned the shared Pest baseline in `tests/Pest.php`
- removed starter example tests that diluted the suite signal
- added semantic-landmark and dashboard-hierarchy guardrails in `tests/Unit/FrontendMaintenanceGuardrailsTest.php`
- added targeted `aria-labelledby` improvements to the welcome, auth, and admin dashboard surfaces

## Phase 9 Follow-Through

- restored architectural roadmap parity with the live flat module and transport layout
- added the `Audit` slice plus the `audit_logs` table for durable operator trace records
- instrumented sensitive admin and settings writes with transactional post-commit audit logging
- added explicit request-level ability and authentication guards to the admin and settings form requests
- added non-unique index support for the current admin filter and sort paths

## Phase 11 Follow-Through

- moved the remaining PHP validation and transformer helpers out of legacy support locations into flat module-owned actions
- added global request IDs plus shared Inertia request context for user-visible error references
- added slow-query threshold logging and structured performance budgets for backend query counts and high-identity first-load responses
- added a build-time frontend asset budget check from the standard `npm run build` path
- centralized stale-session, network, and partial-backend failure copy in the shared frontend request-failure helper

## Phase 12 Follow-Through

- moved shared user identity validation rules into `Modules/Core/app/Actions/UserIdentityValidationRules.php`
- added structured IAM validation exceptions for unknown selections and protected super-admin mutations
- enforced protected `super-admin` invariants for rename, delete, permission sync, last-user deletion, and last-role-removal paths
- added the permission lifecycle listener that keeps the protected `super-admin` role synced with the full permission catalog
- extended architecture coverage to reject non-contract cross-module imports and to pin the shared validation collaborator usage
- extracted the users index, roles index, and audit-log filter panel into dedicated admin feature surfaces with updated frontend maintenance guardrails

## Recommended Order

1. Phase 13: add dedicated keyboard and focus regression coverage on the high-identity auth, admin, and settings surfaces.
2. Deferred platform follow-through: add CI and release-governance automation after deployment tooling and runner requirements are confirmed.
