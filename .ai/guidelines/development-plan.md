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
- Admin application code now lives under `app/Modules/Admin/{Permissions,Roles,Users,Shared}` with slice-owned `Actions`, `Queries`, `DTOs`, `Support`, and `Exceptions`.
- Admin HTTP transport now lives under `app/Http/Admin/{Permissions,Roles,Users}/Controllers|Requests`.
- Settings application code now lives under `app/Modules/Settings`, and settings transport now lives under `app/Http/Settings/{Profile,Password,TwoFactor}/Controllers|Requests`.
- Auth DTOs and Fortify adapter actions now live under `app/Modules/Auth`.
- Legacy PHP class files were removed from `app/Actions`, `app/Support/Data`, `app/Support/Admin`, `app/Http/Controllers/Admin|Settings`, and `app/Http/Requests/Admin|Settings`.
- Wayfinder and TypeScript contracts were regenerated after the controller and form request namespace move.
- Backend architecture tests now enforce module-owned actions, queries, DTOs, support collaborators, and slice-oriented HTTP transport namespaces.
- Selective admin contracts now live under slice-owned `Contracts` namespaces, and `AppServiceProvider` binds the reusable dashboard metrics, permission group, grouped permissions, and admin filter-option collaborators.
- Admin dashboard counts now delegate through a dedicated read-side metrics provider instead of an inline route closure query block.
- Container-backed contract coverage now verifies the new admin bindings, while architecture tests enforce narrow module-owned contract namespaces.
- Phase 5 is now the active workstream.

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
- Shared payloads already use Spatie Data objects under `app/Support/Data`.
- Inertia v3 patterns are already in use.
- The visual system is already stronger than a stock starter.
- Local verification already covers the core backend and frontend release path.

The main remaining problems are structural inconsistencies:

- `AppServiceProvider` has almost no application bindings yet.
- `resources/js/types/page-props.ts` is still a monolithic manual mirror of backend payloads.
- The base test harness still reflects the default starter setup.
- Selective contracts and provider bindings are still mostly absent from the new slice-owned collaborators.

## Target End State

The end state is a domain-driven application where each slice owns its own reads, writes, contracts, DTOs, and supporting logic.

Logical slice shape:

- HTTP layer: controllers, requests, policies, routes
- Application layer: actions, queries, catalogs, assemblers, guards
- Contract layer: narrow interfaces for reusable or swappable collaborators
- Presentation layer: Inertia pages, page prop contracts, Wayfinder route usage, typed frontend modules
- Infrastructure layer: service providers, persistence details, vendor integrations

Preferred slice targets:

- `Admin/Users`
- `Admin/Roles`
- `Admin/Permissions`
- `Auth`
- `Settings`
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
- Avoid growing `resources/js/types/page-props.ts` further.
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
  - moved admin application, DTO, support, and exception classes into `app/Modules/Admin/{Permissions,Roles,Users,Shared}`
  - moved settings application and DTO classes into `app/Modules/Settings`
  - moved auth DTOs and Fortify adapter actions into `app/Modules/Auth`
  - moved admin and settings transport into `app/Http/{Admin|Settings}/{Domain}/Controllers|Requests`
  - deleted legacy PHP class files from the old technical roots once imports were rewired
  - regenerated Wayfinder and TypeScript contracts after the namespace move
  - added Pest architecture coverage for module-owned application code and slice-oriented transport namespaces

- Start placing new and refactored classes into logical domain groupings one slice at a time.
- Prefer co-locating actions, queries, data objects, contracts, and support collaborators by slice.
- Use transitional bridges only when necessary, then delete them quickly.
- Avoid parallel long-term architectures.
- Treat new work as domain-first, even if older files still live in legacy folders during transition.

Key targets:

- `app/Modules/Admin/**`
- `app/Modules/Settings/**`
- `app/Modules/Auth/**`
- `app/Http/Admin/**`
- `app/Http/Settings/**`
- `app/Providers/**`

Definition of done:

- New work lands in domain-oriented slices.
- Shared behavior is clearly identified as shared rather than accidentally global.
- The codebase can keep migrating slice by slice without creating a third architecture style.

## Phase 4: Add Selective Contracts And Provider Bindings

Status: completed on `2026-03-28`.

Completed work:

- added slice-owned contracts for reusable admin collaborators under `app/Modules/Admin/{Dashboard,Permissions,Roles,Users}/Contracts`
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

- Add transactions to every multi-step write workflow.
- Standardize normalization and guard phases before persistence.
- Normalize restore and idempotent behavior for soft-deleted permissions and similar flows.
- Push side effects after commit where appropriate.
- Audit action size, naming, and mutation boundaries across admin, settings, and custom auth-related flows.

Key targets:

- `app/Actions/Admin/**`
- `app/Actions/Settings/**`
- `app/Actions/Fortify/**`
- relevant form requests and data classes

Definition of done:

- Every non-trivial write is transactional and single-purpose.
- Mutation rules are readable, explicit, and testable.
- Action naming and behavior are consistent across slices.

## Phase 6: Reduce PHP/TypeScript Contract Drift

The current prop type mirror is already a maintenance liability and will get worse during the refactor if it is not split early.

- Break `resources/js/types/page-props.ts` into domain-specific modules.
- Derive or co-locate more page prop contracts from existing Spatie Data objects and transformers where practical.
- Keep Wayfinder-generated route usage as the canonical route contract.
- Update frontend pages to consume domain-specific types instead of a single monolithic props file.
- Regenerate contracts when request, route, or DTO shapes change.

Key targets:

- `resources/js/types/page-props.ts`
- `resources/js/types/**`
- `app/Support/Data/**`
- `app/Support/TypeScript/FormRequestRulesTransformer.php`
- `resources/js/pages/**`

Definition of done:

- Page prop types are split or derived by domain.
- DTO changes do not trigger broad manual search-and-replace work in one file.
- Frontend route usage remains type-safe through Wayfinder.

## Phase 7: Harden Auth, Settings, And Operational UX

Auth and settings should remain trustworthy through the architecture shift, not just visually polished.

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

## Phase 8: Finish The Test Harness And Local Delivery Workflow

The final phase reduces daily friction and makes the long refactor sustainable.

- Move the test harness toward `withoutVite()` by default where safe.
- Evaluate `LazilyRefreshDatabase` or other targeted harness improvements for heavy suites.
- Expand the local minimum release path verification:
  - `vendor/bin/pint --dirty --format agent`
  - targeted `php artisan test --compact`
  - browser smoke where appropriate
  - `npm run typecheck`
  - `npm run build`
- Keep local debugging and verification aligned with Boost and Herd tooling.

Key targets:

- `tests/TestCase.php`
- `tests/Pest.php`

Definition of done:

- Local verification is consistent and repeatable.
- JavaScript regressions are caught earlier.
- The refactor remains shippable throughout the migration.

## Explicit Do-Not-Do List

- Do not mix instance `handle()` and static `handle()` conventions across application code.
- Do not move read orchestration into write actions.
- Do not create interfaces for simple leaf actions with no variability.
- Do not instantiate reusable collaborators directly once provider bindings exist.
- Do not keep PHP collection filtering and sorting when SQL can do the work cheaper and more clearly.
- Do not keep growing `resources/js/types/page-props.ts`.
- Do not add dependencies before architecture and harness stability are re-established.
- Do not do a big-bang filesystem move.

## First Four PRs To Open

1. `static-action-standard-and-permissions-normalization`
   - replace stale architecture tests
   - normalize permissions naming and orchestration
   - align local verification with the new baseline

2. `admin-read-write-separation`
   - extract admin read-side collaborators
   - shrink users, roles, and permissions controllers
   - move filtering and sorting into database-backed queries

3. `domain-slices-and-selective-contracts`
   - start moving slices toward domain ownership
   - add narrow contracts for reusable catalogs and providers
   - bind collaborators in service providers

4. `type-auth-and-harness-hardening`
   - split or derive frontend page prop contracts
   - add auth and settings browser smoke coverage
   - improve the test harness and local verification flow

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
