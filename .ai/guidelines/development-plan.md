# Development Plan

Last updated: `2026-04-10`
Owner: `platform`

## Live Architecture Baseline

- Domain modules live in `Modules/{Audit,Auth,Core,Dashboard,Marketing,Permissions,Roles,Settings,Users}`.
- HTTP transport for feature-owned slices now lives inside each module under `Modules/{Domain}/app/Http/**`.
- Cross-module reuse is allowed when it stays coherent and feature-owned; contract indirection is still useful, but it is no longer the only approved path.
- Admin page shells own layout, breadcrumbs, document titles, and query wiring. Feature surfaces own table, mobile-list, and filter-panel markup.
- The current admin dashboard board composition is the accepted baseline. The old dashboard-drift note is retired as stale documentation, not a standing redesign requirement.

## Completed Phases

### Phase 8

- cleaned the shared Pest baseline and removed starter example noise
- added semantic-landmark and dashboard-hierarchy frontend guardrails
- improved high-identity surface semantics across welcome, auth, and admin entry points

### Phase 9

- reconciled the roadmap with the live flat module layout
- added the durable `Audit` slice and transactional audit writes for sensitive admin and settings mutations
- co-located request-level authorization intent inside the admin and settings form requests
- added dedicated admin index-support database indexes

### Phase 11

- moved remaining validation and transformer helpers out of legacy support locations into module-owned actions
- added request IDs, slow-query logging, backend query budgets, and a frontend asset budget gate
- centralized stale-session, network, and partial-backend failure copy

### Phase 12

- replaced the IAM-local `ProfileValidationRules` helper with `Shared\Actions\UserIdentityValidationRules`
- added structured IAM validation exceptions with explicit context payloads for unknown selections and protected super-admin mutations
- enforced protected `super-admin` invariants for rename, delete, permission sync, last-user deletion, and last-role-removal paths
- attached permission lifecycle listeners so the protected `super-admin` role stays synced with the live permission catalog
- extended backend architecture coverage to reject non-contract cross-module imports
- extracted the users index, roles index, and audit-log filter panel into dedicated admin feature surfaces
- removed redundant floating-label placeholders from the audit-log filter controls while preserving existing UI behavior

## Active Next Phase

### Phase 13

- add dedicated keyboard and focus regression coverage on high-identity auth, admin, and settings surfaces
- deepen accessibility assertions beyond landmarks so interaction regressions are caught earlier
- keep the current dashboard composition stable unless a new product requirement justifies a redesign

## Deferred Work

- CI and release-governance automation remain deferred until deployment tooling and runner requirements are confirmed
- do not add `.github` workflows until that decision is explicit
