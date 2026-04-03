# Development Plan

## Purpose

This plan supersedes the previous post-upgrade cleanup plan.

The starter is no longer targeting a loose "clean up the scaffold" direction. The goal now is an incremental refactor that ends in a fully domain-driven architecture while preserving delivery momentum, keeping the starter demo-safe, and avoiding a destabilizing big-bang rewrite.

This plan is aligned with the current application baseline and the updated AGENTS guidance:

- Laravel `13.2.0`
- Inertia Laravel `3.0.1`
- `@inertiajs/vue3` `3.0.0`
- Tailwind CSS `4.2.2`
- Fortify `1.36.2`
- PHP `8.4`

## Confirmed Project Decisions

- Non-Fortify application actions and queries will standardize on a public static `handle()` entrypoint.
- Fortify adapters may continue to use vendor-required contract methods instead of the project `handle()` API.
- The migration path is incremental, but the intended end state is fully domain-driven.
- Divergent areas should be normalized immediately when touched. The admin permissions slice is the first mandatory normalization target.
- Contracts remain selective. Do not introduce interfaces for simple leaf actions or one-off classes.

## Current Status

### 2026-03-27

- Phase 1 is complete.
- Phase 2 is complete.
- Fortify adapter actions remain explicitly exempted where vendor contracts require named methods.
- Admin controller architecture tests now check thin static action delegation for writes and explicit read-side delegation for admin index and supporting page data.
- The admin permissions slice now uses the same `Create` / `Update` / `Delete` action naming and static orchestration pattern as the rest of the app.
- Admin users, roles, and permissions index reads now delegate to explicit read-side query collaborators instead of controller-owned query helpers.
- Roles and permissions filtering and sorting now run in SQL-backed query classes, and admin filter-option assembly has moved into dedicated read-side collaborators.
- Users and roles create/edit support data now comes from dedicated read-side classes instead of controller-private helpers.
- Local Composer verification now includes `style:fix`, `phpstan`, `rector`, `test:type-coverage`, and `test:parallel`.
- Phase 3 is now the active workstream.

### 2026-03-28

- Phase 3 is complete.
- Phase 4 is complete.
- Admin application code now lives under flat module roots such as `app/Modules/{Permissions,Roles,Users,Dashboard,Shared}` with slice-owned `Actions`, `DTOs`, `Contracts`, and `Exceptions`.
- Admin HTTP transport now lives under `app/Http/Controllers/Admin` and `app/Http/Requests/Admin`.
- Settings application code now lives under `app/Modules/Settings`, and settings transport now lives under `app/Http/Controllers/Settings` and `app/Http/Requests/Settings`.
- Auth DTOs and Fortify adapter actions now live under `app/Modules/Auth`.
- Legacy PHP class files were removed from `app/Actions`, `app/Support/Data`, `app/Support/Admin`, `app/Http/Controllers/Admin|Settings`, and `app/Http/Requests/Admin|Settings`.
- Wayfinder and TypeScript contracts were regenerated after the controller and form request namespace move.
- Backend architecture tests now enforce module-owned actions, DTOs, contracts, and slice-oriented HTTP transport namespaces.
- Selective admin contracts now live under slice-owned `Contracts` namespaces, and `AppServiceProvider` binds the reusable dashboard metrics, permission group, grouped permissions, and admin filter-option collaborators.
- Admin dashboard counts now delegate through a dedicated read-side metrics provider instead of an inline route closure query block.
- Container-backed contract coverage now verifies the new admin bindings, while architecture tests enforce narrow module-owned contract namespaces.
- Phase 5 is now the active workstream.

### 2026-03-30

- Phase 5 is complete.
- Admin role creation, permission create/update, and role/permission sync writes now run inside explicit transaction boundaries with separate guard and mutate phases.
- Admin user restoration and Fortify registration now restore soft-deleted user records predictably, clear stale verification and two-factor state, and avoid duplicate-email collisions.
- Role creation now normalizes assignable user IDs before syncing so archived or duplicate selections do not create stale pivot rows.
- Settings profile deletion now performs logout and session invalidation after the delete succeeds instead of before the write.
- Targeted Pest coverage now verifies restored registration behavior, cleaned user restore state, and guarded role-user syncing.
- Phase 6 is now the active workstream.

### 2026-03-31

- Phase 6 is complete.
- Frontend page prop contracts now live in domain-specific modules under `resources/js/types/admin/**` and `resources/js/types/settings.ts` instead of a monolithic `resources/js/types/page-props.ts` file.
- Admin, settings, shared auth, and filter/query payload DTOs that back Inertia pages now carry `#[TypeScript]` so generated frontend contracts follow the backend DTO source of truth more closely.
- Frontend pages, shared admin components, and composables now consume slice-specific type modules instead of one shared page-prop file.
- Wayfinder and TypeScript contracts were regenerated after the DTO export changes.
- Pest architecture coverage now enforces the TypeScript export markers on frontend-bound DTOs and form requests, and it verifies that the monolithic page prop file does not return.
- Phase 7 is complete.
- Pest browser coverage now exercises login, registration, password reset, email verification notice, password confirmation, and the full two-factor enable -> confirm -> recovery -> challenge -> disable flow.
- Browser smoke coverage now checks for JavaScript errors across public auth pages plus key authenticated admin and settings surfaces.
- Auth page prop contracts now have targeted Inertia partial reload coverage for login, forgot password, and email verification notice states.
- Existing settings partial reload coverage and admin deferred/partial reload coverage remain in place as the operational contract baseline.
- Fortify redirect and response behavior was reviewed against the browser flows and kept on the default responses because the current redirects land correctly without extra bindings.
- Phase 8 is now the active workstream.

### 2026-04-02

- Phase 8 is complete.
- Added the canonical Phase 8 audit ledger at `.ai/guidelines/reports/system-audit.md`.
- The audit recorded no unassigned `Critical` findings in the current baseline.
- The audit surfaced three immediate high-priority follow-up tracks:
  - roadmap-to-code structural drift between the documented target architecture and the live namespace layout
  - missing durable audit logging for sensitive admin mutations
  - missing CI parity for the existing local verification gates
- The audit also captured the current known-good verification baseline and a focused admin-dashboard versus `Welcome.vue` composition note.
- Folded the lightweight release-governance and guardrail work from later phases into Phase 8:
  - cleaned the Pest baseline and switched shared feature/browser tests to `LazilyRefreshDatabase`
  - removed starter example tests from the suite
  - added semantic landmark and dashboard-composition guardrails for high-identity frontend surfaces
- Phase 9 is now the active workstream.

### 2026-04-03

- Phase 9 is complete.
- Reconciled the roadmap with the actual flat module and transport layout so the plan no longer claims an `app/Modules/Admin/**` or `app/Http/Admin/**` future that the project rules explicitly reject.
- Added a flat `app/Modules/Audit` slice with durable audit-log persistence for sensitive admin and settings write paths.
- Admin and settings write form requests now co-locate their authorization intent instead of delegating that traceability entirely to route middleware.
- Sensitive role, permission, user, and settings writes now record post-commit audit entries with subject, actor, request context, and before/after payloads.
- Added follow-up database indexes for the current admin filter and sort paths on `users`, `roles`, `permissions`, and `permission_groups`.
- Pest architecture coverage now enforces transactional audited write actions plus explicit guard clauses on privileged form requests, and targeted feature coverage now proves the audit trail on representative admin and settings mutations.
- Phase 10 is now the active workstream.
- Phase 11 groundwork is now in place:
  - request IDs now flow through response headers and shared Inertia props
  - slow-query thresholds now log actionable structured warnings with request context
  - stale-session and partial-failure handling now degrade through shared frontend request-failure helpers
  - enforceable backend query-count and response-size budgets now live in feature coverage
  - frontend asset budgets now run as part of the production build path

## Required Skills And Tooling

### Skills

Implementation work should keep using the relevant AGENTS skills by domain:

- `laravel-best-practices`
- `pest-testing`
- `inertia-vue-development`
- `wayfinder-development`
- `fortify-development`
- `spatie-laravel-php-standards`
- `create-dto-action`
- `scaffold-module` when creating or moving domain slices
- `task-finalization`
- `tailwindcss-development` and the design skills when UI work is involved

### Tool-First Workflow

Prefer the project tooling that actually exists in the current environment:

- Use Herd site, PHP, and service tools instead of ad hoc local environment commands when environment details matter.
- Prefer the Composer scripts defined in `composer.json` for verification work.
- Regenerate Wayfinder and TypeScript contracts when route, request, or DTO contracts change.
- Read sibling files before introducing a new pattern in a touched slice.

## Current Baseline

The codebase already has useful structure, but it still mixes multiple architectural directions:

- Admin and settings writes now use static `handle()` actions outside controllers.
- Shared payloads now use Spatie Data objects inside slice-owned `app/Modules/**/DTOs` namespaces.
- Inertia v3 patterns are already in use.
- The visual system is already stronger than a stock starter.
- Local verification already covers the core backend and frontend release path.

The main remaining problems are now frontend-system follow-through items:

- The admin dashboard composition still drifts away from the `Welcome.vue` shell baseline.
- The accessibility guardrails are stronger, but keyboard and focus regression coverage is still thinner than the desired end state.

## Target End State

The end state is a domain-driven application where each slice owns its own reads, writes, contracts, DTOs, and supporting logic.

Logical slice shape:

- HTTP layer: controllers, requests, policies, routes
- Application layer: actions, queries, catalogs, assemblers, guards
- Contract layer: narrow interfaces for reusable or swappable collaborators
- Presentation layer: Inertia pages, page prop contracts, Wayfinder route usage, typed frontend modules
- Infrastructure layer: service providers, persistence details, vendor integrations

Preferred slice targets:

- `Users`
- `Roles`
- `Permissions`
- `Auth`
- `Settings`
- `Dashboard`
- shared platform services only when the code is truly cross-slice

This does not require an immediate filesystem rewrite. The migration should move slice by slice until the domain-first shape becomes the dominant organization model.

## Non-Negotiable Standards

### Complexity

- Keep cyclomatic complexity per function at `<= 7`; split early at `5`.
- Keep methods below `80` lines and target `<= 30` for new code.
- Use guard clauses and phase separation: parse -> validate -> normalize -> act.
- Controllers, jobs, and listeners must not become workflow coordinators.

### Actions And Queries

- Application actions and queries should expose a public static `handle()` entrypoint.
- Private helper methods are allowed when they simplify the main entrypoint.
- Additional callable methods are allowed when they materially improve clarity or reuse, but they must also be `public static`.
- Use clear naming that reflects intent: `Create`, `Update`, `Delete`, `Sync`, `Index`, `List`, `Build`, `Load`.

### Read/Write Separation

- Writes live in action classes.
- Reads live in query, catalog, provider, or assembler classes.
- DTO mapping belongs in Spatie Data objects or dedicated presentation assemblers.
- Database filtering and sorting should happen in SQL unless the transformation is strictly presentation-only.

### Contracts And DI

- Introduce contracts only when reuse, variability, or testing value is real.
- Bind contracts in service providers, not ad hoc.
- Keep variability behind collaborators, not behind interfaces for every action.
- Avoid `new` for reusable collaborators once a binding exists.

### Frontend And UX

- Keep Wayfinder as the canonical route contract on the frontend.
- Keep page prop contracts split by domain under `resources/js/types/**` instead of reintroducing a shared monolith.
- Every async or deferred state needs an intentional loading and empty state.
- Auth, admin, settings, and marketing surfaces must continue to feel like one authored system.

### Verification

- Every substantial refactor must add or update targeted tests.
- Architecture rules should be enforced in Pest, not carried only in team memory.
- PHP changes should continue to go through Pint.
- Frontend contract and build checks remain part of the release path.

## Recommended Delivery Order

## Phase 1: Freeze The New Standard And Remove Stale Rules

This phase is first because the current tests still encode the old action convention while the live code already contains a second pattern.

Status: completed on `2026-03-27`.

- Replace the backend architecture tests so they enforce the new static `handle()` rule for application actions and queries.
- Keep Fortify adapter actions explicitly exempted where vendor contracts require named methods.
- Replace stale controller architecture rules with tests that reflect the thin-controller, domain-delegation standard.
- Normalize the admin permissions slice immediately so it stops being a special-case architecture island.
- Align local verification around the real baseline that now exists.

Key targets:

- `tests/Unit/BackendArchitectureContractsTest.php`
- `tests/Unit/AdminControllerArchitectureGuidelinesTest.php`
- `app/Http/Controllers/Admin/PermissionsController.php`
- `app/Actions/Admin/Permissions/*`

Definition of done:

- The test suite enforces the `handle()` entrypoint standard.
- Permissions no longer diverges from the chosen architecture.
- Local verification covers the agreed minimum release path.

## Phase 2: Normalize Admin Read/Write Separation

The admin area remains the highest-value operational proof of the starter, so it should become the first clean domain boundary.

Status: completed on `2026-03-27`.

- Extract explicit read-side queries, catalogs, and payload builders for users, roles, and permissions.
- Remove collection-heavy filtering and sorting from controller paths when the database can do the work.
- Keep controllers limited to request translation, authorization, response choice, and delegation.
- Separate index-list orchestration from edit-page payload assembly.
- Move filter-option assembly into dedicated read-side classes instead of controller helpers.

Key targets:

- `app/Http/Controllers/Admin/UsersController.php`
- `app/Http/Controllers/Admin/RolesController.php`
- `app/Http/Controllers/Admin/PermissionsController.php`
- `app/Support/Admin/Shared/AdminIndexQuery.php`
- `app/Support/Admin/Roles/GroupedPermissions.php`
- `app/Support/Admin/Permissions/PermissionGroupCatalog.php`
- `app/Actions/Admin/*`

Definition of done:

- Admin index endpoints are thin and boring.
- Roles and permissions filtering and sorting are database-driven.
- Each admin slice has explicit write actions and explicit read-side collaborators.

## Phase 3: Introduce Domain Slices Incrementally

This phase shifts the starter from shared technical folders toward domain ownership without forcing a big-bang move.

Status: completed on `2026-03-28`.

- Completed work:
  - moved admin application, DTO, collaborator, and exception classes into flat module roots under `app/Modules/{Permissions,Roles,Users,Dashboard,Shared}`
  - moved settings application and DTO classes into `app/Modules/Settings`
  - moved auth DTOs and Fortify adapter actions into `app/Modules/Auth`
  - consolidated admin and settings transport into `app/Http/Controllers/{Admin,Settings}` and `app/Http/Requests/{Admin,Settings}`
  - deleted legacy PHP class files from the old technical roots once imports were rewired
  - regenerated Wayfinder and TypeScript contracts after the namespace move
  - added Pest architecture coverage for module-owned application code and slice-oriented transport namespaces

- Start placing new and refactored classes into logical domain groupings one slice at a time.
- Prefer co-locating actions, queries, data objects, contracts, and support collaborators by slice.
- Use transitional bridges only when necessary, then delete them quickly.
- Avoid parallel long-term architectures.
- Treat new work as domain-first, even if older files still live in legacy folders during transition.

Key targets:

- `app/Modules/{Permissions,Roles,Users,Dashboard,Shared}/**`
- `app/Modules/Settings/**`
- `app/Modules/Auth/**`
- `app/Http/Controllers/{Admin,Settings}/**`
- `app/Http/Requests/{Admin,Settings}/**`
- `app/Providers/**`

Definition of done:

- New work lands in domain-oriented slices.
- Shared behavior is clearly identified as shared rather than accidentally global.
- The codebase can keep migrating slice by slice without creating a third architecture style.

## Phase 4: Add Selective Contracts And Provider Bindings

Status: completed on `2026-03-28`.

Completed work:

- added slice-owned contracts for reusable admin collaborators under `app/Modules/{Dashboard,Permissions,Roles,Users}/Contracts`
- bound those contracts in `AppServiceProvider` using explicit singleton registrations
- introduced slice-specific filter-option catalog implementations for users, roles, and permissions
- updated permissions write paths and admin read-side queries/controllers to depend on contracts rather than concrete collaborators
- extracted admin dashboard counts into a dedicated read-side metrics provider and query
- added Pest coverage for module-owned contracts and container bindings so the new boundary is enforced

Static action entrypoints do not remove the need for good boundaries. Variability belongs behind collaborators with narrow contracts.

- Introduce contracts only for collaborators with real reuse, swap potential, or testing value.
- First candidates:
  - permission group catalog/provider
  - grouped permissions provider
  - admin dashboard metrics provider
  - reusable option catalogs across admin slices
- Bind contracts in service providers using container bindings or singletons.
- Keep the primary action/query entrypoints static; place variability behind bound collaborators.
- Evaluate deferred providers where a provider only registers bindings.

Key targets:

- `app/Providers/AppServiceProvider.php`
- `app/Providers/FortifyServiceProvider.php`
- approved contract locations for domain collaborators
- slice-specific catalog and provider classes

Definition of done:

- Reusable collaborators are swappable and testable.
- Container usage becomes explicit and deliberate.
- The app avoids speculative interfaces for simple leaf classes.

## Phase 5: Harden Write Workflows

Once the boundaries are stable, the write side needs to become predictably safe and uniform.

Status: completed on `2026-03-30`.

Completed work:

- wrapped multi-step admin writes in transactions for role creation, permission creation and updates, and role/permission sync workflows
- split sync and create actions into explicit guard and mutate phases so requested roles, permissions, and assignable users are resolved before persistence
- normalized soft-delete restore behavior for admin user creation and Fortify registration, including clearing stale verification and two-factor state on restore
- moved profile-destroy auth/session side effects to occur after the delete succeeds
- added targeted Pest coverage for restored registration, restored user state cleanup, and guarded role assignment behavior

- Add transactions to every multistep write workflow.
- Standardize normalization and guard phases before persistence.
- Normalize restore and idempotent behavior for soft-deleted permissions and similar flows.
- Push side effects after commit where appropriate.
- Audit action size, naming, and mutation boundaries across admin, settings, and custom auth-related flows.

Key targets:

- `app/Modules/Permissions/**/*`
- `app/Modules/Roles/**/*`
- `app/Modules/Settings/**/*`
- `app/Modules/Shared/**/*`
- `app/Modules/Users/**/*`
- relevant form requests and data classes

Definition of done:

- Every non-trivial write is transactional and single-purpose.
- Mutation rules are readable, explicit, and testable.
- Action naming and behavior are consistent across slices.

## Phase 6: Reduce PHP/TypeScript Contract Drift

The current prop type mirror is already a maintenance liability and will get worse during the refactor if it is not split early.

Status: completed on `2026-03-31`.

Completed work:

- removed `resources/js/types/page-props.ts` and replaced it with slice-specific frontend type modules under `resources/js/types/admin/**` and `resources/js/types/settings.ts`
- annotated the auth, shared admin index, users, roles, and permissions DTOs that back page payloads with `#[TypeScript]`
- regenerated TypeScript and Wayfinder contracts after the DTO export changes
- rewired admin pages, settings pages, and shared admin composables/components onto the new domain-local type modules
- tightened frontend aliases around generated array and nullable DTO fields where the transformer output is intentionally broader than the runtime payload
- added Pest architecture checks to keep the generated TypeScript markers and split page-prop structure in place

- Break `resources/js/types/page-props.ts` into domain-specific modules.
- Derive or co-locate more page prop contracts from existing Spatie Data objects and transformers where practical.
- Keep Wayfinder-generated route usage as the canonical route contract.
- Update frontend pages to consume domain-specific types instead of a single monolithic props file.
- Regenerate contracts when request, route, or DTO shapes change.

Key targets:

- `resources/js/types/admin/**`
- `resources/js/types/settings.ts`
- `resources/js/types/**`
- `app/Modules/**/DTOs/**`
- `app/Support/TypeScript/FormRequestRulesTransformer.php`
- `resources/js/pages/**`

Definition of done:

- Page prop types are split or derived by domain.
- DTO changes do not trigger broad manual search-and-replace work in one file.
- Frontend route usage remains type-safe through Wayfinder.

## Phase 7: Harden Auth, Settings, And Operational UX

Auth and settings should remain trustworthy through the architecture shift, not just visually polished.

Status: completed on `2026-03-31`.

- Completed work:
  - added Pest browser coverage for login, registration, password reset, email verification notice, password confirmation, and the full two-factor lifecycle
  - added JavaScript error smoke coverage for public auth pages and key authenticated admin/settings pages
  - added targeted Inertia partial reload coverage for login, forgot password, and email verification notice props
  - verified the existing settings partial reload and admin deferred prop contracts still cover the intended async page behavior
  - reviewed Fortify response bindings and kept the framework defaults because the current redirect behavior is already correct for the browser flows

- Add browser smoke coverage for:
  - login
  - registration if enabled
  - password reset
  - email verification notice
  - password confirmation
  - two-factor enable -> confirm -> recovery codes -> disable
- Verify partial reload and deferred prop behavior on auth, settings, and admin pages.
- Review Fortify response bindings only where redirect behavior truly needs customization.
- Add JavaScript error smoke coverage for key pages and flows.
- Keep the Southeast Code visual system consistent across auth, admin, and settings.

Key targets:

- `app/Providers/FortifyServiceProvider.php`
- `app/Http/Controllers/Settings/**`
- `resources/js/pages/auth/**`
- `resources/js/pages/settings/**`
- `resources/js/pages/admin/**`
- `tests/Feature/Auth/**`
- `tests/Feature/Settings/**`
- `tests/Browser/**`

Definition of done:

- Core auth and settings flows are demo-safe and browser-verified.
- Deferred and partial reload contracts are covered.
- No major page introduces a second design language or missing async states.

## Phase 8: Baseline Quality Audit And Gap Register

This phase replaces the prior "final" phase and becomes the new gate before any additional feature work. The codebase has strong foundations, but a full-system audit is still missing.

Status: completed on `2026-04-02`.

Completed work:

- created the canonical audit ledger at `.ai/guidelines/reports/system-audit.md`
- recorded severity-ranked findings across backend, security, data, frontend, UX, dashboard composition, and tooling integrity
- mapped each finding to an owner area and follow-up phase
- captured a dated known-good verification baseline for backend and frontend quality gates
- documented dashboard drift against the `Welcome.vue` baseline and identified the missing guardrails needed for Phase 10
- folded in the lightweight later-phase work that did not require major code changes:
  - Pest harness cleanup and `LazilyRefreshDatabase` adoption
  - removal of starter example tests
  - semantic-landmark and dashboard-hierarchy frontend guardrails

Scope of the audit:

- Backend architecture conformance (thin controllers, static action/query orchestration, DTO boundaries, container contracts).
- Security and compliance posture (authorization coverage, policy/gate consistency, audit logging and sensitive-action traceability).
- Data and query efficiency (N+1 risk scan, indexing review for filter/sort columns, pagination/query-shape consistency).
- Frontend UI quality (visual hierarchy, accessibility, responsive behavior, empty/loading/error states, keyboard support).
- UX coherence and branding fidelity (cross-surface consistency between marketing, auth, settings, and admin).
- Admin dashboard composition fidelity against the `Welcome.vue` baseline (dominant shell, integrated action/media stage, restrained supporting bands, and avoidance of generic metric-card admin layouts).
- Tooling and delivery integrity (test harness behavior, CI parity, static analysis/type safety, build diagnostics).

Required outputs:

- A single audit ledger in `.ai/guidelines/reports/system-audit.md` with severity-tagged findings (`Critical`, `High`, `Medium`, `Low`).
- A cross-reference matrix mapping each finding to a target phase and owner area (`backend`, `frontend`, `platform`).
- A "known-good baseline" report for current tests and checks so regressions can be measured explicitly.
- A focused dashboard-vs-welcome comparison note that identifies structural drift, reusable primitives, and any missing guardrails needed to keep admin from becoming a second design language.

Key targets:

- `app/**`
- `resources/js/**`
- `resources/css/**`
- `routes/**`
- `tests/**`
- `composer.json`
- `package.json`

Definition of done:

- Every major subsystem has a written, severity-ranked audit.
- No unresolved Critical issue remains unassigned to a phase.
- The team has one canonical quality ledger instead of scattered TODOs.

## Phase 9: Backend Domain Completion, Security, And Data Integrity

After the audit ledger exists, complete backend hardening and domain consistency.

Status: completed on `2026-04-03`.

- Normalize all remaining cross-slice inconsistencies in action/query naming, static entrypoints, and DTO hydration paths.
- Add or tighten authorization for every admin/settings write path using policies or gates where missing.
- Introduce explicit audit trails for sensitive mutations (role changes, permission changes, destructive user actions).
- Validate index/filter query paths against real database indexes; add follow-up migrations for missing indexes.
- Finish selective contract extraction only where variability or testability is real; avoid speculative interfaces.
- Add architectural guardrails for transaction boundaries and post-commit side effects.

Key targets:

- `app/Modules/**`
- `app/Http/**`
- `app/Providers/**`
- `database/migrations/**`
- `tests/Feature/Admin/**`
- `tests/Feature/Settings/**`
- `tests/Unit/BackendArchitectureContractsTest.php`

Definition of done:

- Sensitive writes are authorized, transactional, and auditable.
- Query-heavy views have index-backed filter/sort paths.
- Backend architecture rules are enforced by tests instead of conventions.

## Phase 10: Frontend UX, Accessibility, And Branding System Perfection

This starter already has a strong visual direction; this phase turns it into a documented, measurable design system that scales.

Lightweight tasks already completed in Phase 8:

- added semantic landmark guardrails for high-identity surfaces
- added dashboard hierarchy guardrails that keep the admin landing page anchored to the `Welcome.vue` shell structure baseline

- Run a full accessibility pass (focus order, keyboard-only flows, semantic landmarks, ARIA quality, contrast validation).
- Standardize shared page-state patterns for loading, empty, success, and failure experiences.
- Unify motion behavior and interaction affordances across marketing, auth, admin, and settings surfaces.
- Redesign the admin dashboard landing experience so it follows the `Welcome.vue` composition model: one dominant shell, one clear primary operational message, one integrated action/media or action/evidence stage, and one lower supporting band instead of stacked equal-weight panels.
- Replace any ad hoc UI implementation differences with reusable Reka-UI/Tailwind component primitives.
- Tighten copy and information architecture so each page has one clear primary action and reduced cognitive load.
- Capture brand system guidance (tone, type rhythm, spacing cadence, component usage) in reusable docs.
- Add frontend guardrails that explicitly reject generic admin dashboard tropes when they conflict with the Southeast Code baseline, especially repetitive metric-card grids and filler status panels on the dashboard landing page.

Key targets:

- `resources/js/pages/**`
- `resources/js/components/**`
- `resources/js/layouts/**`
- `resources/css/app.css`
- `tests/Browser/**`
- `tests/Unit/*Ui*Test.php`
- `tests/Unit/FrontendMaintenanceGuardrailsTest.php`

Definition of done:

- Core journeys pass keyboard-first and screen-reader sanity checks.
- Cross-surface UI feels authored as one system, not separate implementations.
- Shared components own the majority of recurring interaction and visual patterns.
- The admin dashboard visibly inherits the `Welcome.vue` narrative structure and no longer reads like a generic SaaS metrics shell.

## Phase 11: Performance, Resilience, And Operational Excellence

Move from "works well" to "scales predictably" on both backend and frontend.

- Backend code has no further opportunity for refactors/changes that move code that isn't a route, controller or middleware into a module. Requests, DTO's, actions, responses, models and module specific traits/concerns/contracts should all be located in an appropriate directory within a module.
- There are no `DB::` class calls in the code that can be accomplished using Eloquent, or can have code added to a model allowing any DB calls to be removed.
- As many exceptions as possible are not generic, but rather are named logically and allow a developer to find a module->class->method->line and context to track down the cause of the exception.
- Ensure graceful degraded behavior for network errors, stale sessions, and partial backend failures.

Key targets:

- `app/Modules/Shared/**`
- `app/Exceptions/**`
- `resources/js/pages/admin/**`
- `resources/js/composables/**`
- `vite.config.ts`
- `tests/Feature/**`
- `tests/Browser/**`

Definition of done:

- Measured performance budgets exist and are enforceable.
- Error conditions are observable and user-safe.
- High-volume scenarios remain responsive and stable.
- Minimal code lives outside modules
- Backend code is DRY, SOLID and is leveraging Laravel 13's attribute system

## Phase 12: Delivery Pipeline, Developer Experience, And Release Governance

Reframe the old harness-focused phase as a broader release-governance phase.

Lightweight tasks already completed in Phase 8:

- (removed, this was not yet accomplished) added GitHub Actions CI parity for backend, frontend, and browser smoke checks
- cleaned the shared Pest baseline and removed leftover starter example tests
- Establish performance budgets for first-load payload, JS bundle segments, and key admin index queries.
- Add server-side observability hooks (slow query thresholds, actionable structured logs, failure correlation IDs).
- Validate deferred props, partial reloads, and pagination behavior under realistic data volumes.
- Optimize expensive frontend surfaces (table rendering, filter interactions, expensive reactive recomputations).
- Finalize deterministic local and CI verification pipelines (backend + frontend + browser smoke tiers).
- Introduce tiered test strategy (`quick`, `full`, `release`) with explicit commands and expected runtime budgets.
- Move test harness optimizations forward (`withoutVite()` defaults where safe, targeted `LazilyRefreshDatabase` adoption, parallel-safe fixtures).
- Add pre-release checklists for architecture drift, generated contract drift (Wayfinder/TypeScript), and UX regression checks.
- Add release-time UI regression checks for high-identity surfaces, including explicit guardrails or visual assertions that keep the admin dashboard aligned with the `Welcome.vue` shell and hierarchy.
- Document incident rollback playbooks and release cut criteria for this starter kit.

Expanded local minimum release path:

- `vendor/bin/pint --dirty --format agent`
- `php artisan test --compact`
- `php artisan test --parallel --compact`
- `npm run typecheck`
- `npm run build`
- browser smoke tests for auth/admin/settings critical flows

Key targets:

- `tests/TestCase.php`
- `tests/Pest.php`
- `.ai/guidelines/**`
- `composer.json`
- `package.json`
- `tests/Unit/FrontendMaintenanceGuardrailsTest.php`
- `tests/Browser/**`

Definition of done:

- Local and CI release checks are aligned and repeatable.
- Contract drift and UI regressions are caught before merge.
- The starter has explicit release governance, not just ad hoc verification.
- High-identity pages such as `Welcome.vue` and the admin dashboard have enforceable regression coverage, not just informal design intent.

## Explicit Do-Not-Do List

- Do not mix instance `handle()` and static `handle()` conventions across application code.
- Do not move read orchestration into write actions.
- Do not create interfaces for simple leaf actions with no variability.
- Do not instantiate reusable collaborators directly once provider bindings exist.
- Do not keep PHP collection filtering and sorting when SQL can do the work cheaper and more clearly.
- Do not reintroduce a monolithic frontend page prop types file.
- Do not add dependencies before architecture and harness stability are re-established.
- Do not do a big-bang filesystem move.

## Next Four PRs To Open

1. `phase-8-system-audit-ledger`
   - produce the complete severity-ranked audit ledger
   - map all findings to backend/frontend/platform ownership
   - establish the known-good verification baseline snapshot

2. `phase-9-backend-security-and-data-integrity`
   - close backend architecture and authorization gaps
   - harden sensitive writes with transaction + audit trace coverage
   - add missing index migrations for high-frequency filter/sort paths

3. `phase-10-frontend-a11y-and-brand-system`
   - standardize page-state patterns across auth/admin/settings/marketing
   - complete keyboard, semantic, and contrast hardening
   - document and enforce reusable visual/interaction system rules

4. `phase-11-12-performance-and-release-governance`
   - add measurable performance/observability budgets
   - finalize deterministic quick/full/release verification tiers
   - codify release checklist and rollback playbooks

## Success Criteria

The starter is on the right path when these statements are true:

- Controllers are thin and boring.
- Application actions and queries follow one clear static `handle()` convention.
- Domain slices are becoming the dominant organization model.
- Contracts exist only where they create leverage.
- Reads are database-driven and reusable.
- Type drift between PHP and TypeScript is shrinking.
- Auth, admin, and settings flows are browser-verified and demo-safe.
- The UI remains authored, consistent, and transferable across marketing and product surfaces.
