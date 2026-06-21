# Architecture Decision Points

Source reviewed: the requested `./docs/roadmap.md` file is not present in this repository at the time of writing. This decision inventory is therefore grounded in the live baseline in `.ai/guidelines/development-plan.md`, the existing Laravel/Inertia/Vue codebase, and the current module, route, component, and test structure.

## 1. Product Scope and Release Strategy

- Decide whether the application remains an admin/IAM/settings starter kit or becomes a specific business-domain product with additional bounded contexts.
- Decide which user roles are considered first-class personas beyond `super-admin`, administrators, and standard authenticated users.
- Decide whether public, authenticated, admin, and settings areas should remain the only top-level surfaces.
- Decide whether future domain modules should be shipped behind feature flags, permission gates, or separate deployments.
- Decide whether a formal minimum viable product milestone exists before Phase 13 accessibility hardening is completed.
- Decide whether roadmap phases remain sequential or whether infrastructure, UX, and feature work can run in parallel.
- Decide how completed, active, and deferred work are promoted from planning documents into issue tracking.
- Decide whether each phase requires sign-off criteria, demo artifacts, or screenshots before merging.
- Decide whether architectural decisions should be captured as ADRs in `docs/adr`.
- Decide how to handle roadmap drift when live code diverges from planning documents.

## 2. Repository, Branching, and Governance

- Decide the canonical branch strategy for feature branches, release branches, hotfixes, and documentation-only work.
- Decide whether pull requests require linked issues, checklists, screenshots, testing evidence, or architecture notes.
- Decide whether CI remains deferred or must be introduced before additional feature development.
- Decide which checks are mandatory locally while CI is unavailable.
- Decide whether `.github` workflows remain forbidden until deployment and runner requirements are confirmed.
- Decide who owns dependency upgrades across Composer, npm, Laravel, Inertia, Vue, Tailwind, and browser tooling.
- Decide how generated files from Wayfinder, Ziggy-like routing, IDE helpers, and TypeScript transformers are committed.
- Decide the policy for lockfile updates and reproducible builds.
- Decide whether documentation updates are required for every feature PR.
- Decide whether code owners or module owners should be formalized.

## 3. Modular Architecture Boundaries

- Decide whether the current flat module layout under `app/Modules/{Audit,Dashboard,IAM,Settings,Shared}` remains permanent.
- Decide what criteria justify creating a new module instead of extending an existing module.
- Decide whether module internals can reference other modules through contracts only, with `Shared` as the sole broad reuse location.
- Decide whether HTTP controllers and requests stay in `app/Http` or move into module-owned transport folders.
- Decide whether each module must expose explicit contracts for data providers, policies, events, and query services.
- Decide how to name module actions, DTOs, events, exceptions, listeners, models, and requests consistently.
- Decide whether module service providers are introduced as modules grow.
- Decide whether module-specific migrations and seeders are co-located or remain in Laravel defaults.
- Decide how to enforce architecture guardrails in tests as modules expand.
- Decide whether admin resource CRUD follows one standard module scaffold workflow.

## 4. Backend Application Patterns

- Decide which operations require single action classes versus model methods or controller-local logic.
- Decide which request payloads require DTOs and which can remain primitive or collection-based.
- Decide whether DTOs use Spatie Laravel Data throughout all modules.
- Decide how to transform form request rules into frontend-consumable validation metadata.
- Decide whether every mutating action emits an auditable domain event.
- Decide whether every action should be invokable, dependency-injected, and transaction-aware by default.
- Decide whether controllers may ever contain branching business logic.
- Decide whether model observers, domain events, or action-level explicit calls own side effects.
- Decide how much query logic belongs in actions versus reusable query builders.
- Decide whether soft deletes, pruning, and restore flows are standard for admin-managed resources.

## 5. Authentication, Authorization, and IAM

- Decide whether Laravel Fortify remains the auth backend for all authentication flows.
- Decide whether registration is public, invitation-only, admin-created, or disabled in production.
- Decide whether email verification is mandatory before access to admin, settings, or all authenticated areas.
- Decide whether two-factor authentication is optional, required for admins, or required for all users.
- Decide whether recovery codes can be regenerated without password confirmation.
- Decide session lifetime, remember-me behavior, password confirmation timeout, and concurrent-session policy.
- Decide password rules, compromised-password checks, password reuse restrictions, and password rotation policy.
- Decide whether user deletion is hard delete, soft delete, anonymization, or deactivation.
- Decide whether the existing protected `super-admin` invariants are sufficient for all future permissions.
- Decide how permissions are grouped, named, seeded, localized, and synchronized.
- Decide whether roles are global or can become tenant-, team-, workspace-, or module-scoped.
- Decide whether direct user permissions are allowed or role assignment is the only standard path.
- Decide how role/permission changes invalidate sessions or cached abilities.
- Decide whether authorization uses policies, gates, permission middleware, controller requests, or a layered combination.
- Decide how frontend ability checks mirror backend authorization without becoming a source of truth.

## 6. Admin Experience and Resource Management

- Decide the standard information architecture for admin pages, breadcrumbs, headings, intros, and page actions.
- Decide whether admin index pages always use the shared query, table, mobile-list, and filter-card patterns.
- Decide which admin resources need modal routes versus full-page create/edit flows.
- Decide how bulk actions, destructive actions, exports, imports, and restore flows should behave.
- Decide whether admin tables support persisted column visibility, density, sorting, and saved views.
- Decide how pagination, search, filters, and sorting are encoded in query strings.
- Decide whether filter state should persist across sessions or reset per visit.
- Decide standard empty, loading, skeleton, error, and no-results states.
- Decide whether admin audit history appears inline, in side panels, or on dedicated history pages.
- Decide how protected resources communicate why actions are disabled.

## 7. Audit Logging and Compliance

- Decide which events are security-sensitive enough to require audit records.
- Decide whether reads, exports, failed authorization attempts, and failed validation attempts are audited.
- Decide retention duration, pruning policy, storage growth budget, and archival strategy for audit logs.
- Decide whether audit logs are immutable and whether administrators can redact sensitive values.
- Decide which fields are safe to store in audit metadata and which must be masked.
- Decide whether request IDs, IP addresses, user agents, actor labels, and impersonation context are mandatory.
- Decide whether audit logs must support legal hold, export, or external SIEM forwarding.
- Decide how audit history diffs are formatted for scalar, array, object, and relationship changes.
- Decide timezone and date formatting rules for audit displays.
- Decide whether audit logs require dedicated database partitioning or indexes as volume grows.

## 8. Dashboard and Reporting

- Decide whether the current admin dashboard composition remains stable through new feature phases.
- Decide which metrics are product-critical, operational, security-focused, or vanity metrics.
- Decide whether dashboard metrics are real-time, cached, queued, or snapshot-based.
- Decide how dashboard providers register metric cards and source summaries.
- Decide whether dashboard cards link to filtered admin index views.
- Decide who can see each dashboard metric and whether metrics are permission-scoped.
- Decide how empty data, failed metric queries, and slow providers are surfaced.
- Decide whether charts are needed and whether they can be built with the approved stack without extra libraries.
- Decide whether reports need export, scheduling, or email delivery.
- Decide query budgets for dashboard requests as data volume increases.

## 9. Frontend Architecture and Inertia Contracts

- Decide whether every route-generating frontend call must use Wayfinder-generated helpers.
- Decide whether Inertia page props are fully typed and generated from backend DTOs.
- Decide how shared props are versioned, minimized, and documented.
- Decide whether page components can contain data shaping or must delegate to composables.
- Decide how layouts communicate document titles, breadcrumbs, page intros, and navigation state.
- Decide when to use Inertia `<Form>` versus `useForm`, router visits, or lower-level HTTP helpers.
- Decide how partial reloads, deferred props, prefetching, and optimistic updates are standardized.
- Decide whether frontend authorization gates are component-level, composable-level, route-level, or all three.
- Decide how stale sessions, CSRF failures, network failures, and validation errors are displayed consistently.
- Decide how SSR is supported, tested, or explicitly constrained.

## 10. UI System, Accessibility, and Interaction Design

- Decide whether the Material Design 3-inspired form and button patterns become formal design-system rules.
- Decide which Reka-UI primitives are canonical for dialogs, menus, selects, popovers, tabs, tooltips, and comboboxes.
- Decide whether all icons must use Lucide-Vue and how icon size/stroke conventions are enforced.
- Decide how CVA, `clsx`, and `tailwind-merge` are used for component variants.
- Decide how Tailwind CSS 4 tokens, OKLCH colors, dark mode, radius, shadows, and motion tokens are named.
- Decide keyboard interaction standards for navigation, dialogs, tables, forms, and destructive confirmations.
- Decide accessibility acceptance criteria beyond semantic landmarks, including focus order, visible focus, labels, errors, contrast, target size, and reduced motion.
- Decide whether browser-based accessibility tests are required for every high-identity surface.
- Decide how responsive behavior is specified for mobile, tablet, desktop, and wide admin layouts.
- Decide whether animated micro-interactions are allowed on auth/admin/settings surfaces and how reduced motion is honored.
- Decide how long labels, translated strings, empty values, and text overflow are handled.
- Decide whether screenshots or visual regression tests become required for perceptible UI changes.

## 11. Data Model and Database Strategy

- Decide whether MySQL remains the only supported database target.
- Decide naming conventions for tables, foreign keys, polymorphic relations, indexes, and constraints.
- Decide whether UUIDs, ULIDs, or auto-incrementing IDs are used for new domain entities.
- Decide whether created/updated/deleted timestamps are mandatory for all domain tables.
- Decide how multi-column indexes are designed for admin filters and audit queries.
- Decide whether migrations may backfill data synchronously or require queued/online migration patterns.
- Decide whether seeders are environment-aware and idempotent.
- Decide how test factories are organized per module.
- Decide how data retention, anonymization, and GDPR-style erasure are modeled.
- Decide how database transactions wrap actions that write domain data plus audit events.

## 12. Validation, Errors, and Resilience

- Decide whether all validation lives in form requests and DTO constructors or can exist in actions.
- Decide how validation messages are localized and shared with the frontend.
- Decide standard exception types for domain validation, protected-resource violations, and unknown selections.
- Decide how exceptions are rendered for Inertia visits, API-like requests, and background jobs.
- Decide how retryable, non-retryable, and user-actionable failures are classified.
- Decide whether global toasts, inline banners, field errors, and modal errors have a documented hierarchy.
- Decide how stale form data and concurrent edits are detected.
- Decide whether optimistic locking or updated-at conflict checks are required.
- Decide how rate limiting and abuse prevention are applied to auth and admin mutations.
- Decide how maintenance mode and partial backend outages are communicated.

## 13. Testing and Quality Gates

- Decide which Pest test layers are required for each feature: unit, feature, browser, architecture, accessibility, and performance.
- Decide whether browser tests run locally, in CI, or only before releases.
- Decide minimum coverage expectations for actions, requests, policies, components, and composables.
- Decide whether architecture tests block cross-module imports, fat controllers, untyped props, and hardcoded routes.
- Decide whether frontend unit tests parse Vue component contracts or rely on browser-level checks.
- Decide whether every bug fix requires a regression test.
- Decide how test data avoids depending on seed order or global state.
- Decide how performance budgets are measured for backend queries and frontend assets.
- Decide how flaky tests are quarantined, fixed, or removed.
- Decide whether documentation linting or markdown link checks are required.

## 14. Performance, Observability, and Operations

- Decide request latency budgets for welcome, auth, settings, admin index, dashboard, and audit pages.
- Decide query-count budgets and slow-query thresholds per route category.
- Decide frontend bundle budgets per entry point and when code splitting is required.
- Decide caching policy for permissions, dashboard metrics, form metadata, and navigation props.
- Decide whether logs are structured and how request IDs propagate through backend and frontend reports.
- Decide which operational metrics, traces, and alerts are needed before production.
- Decide whether queues are required for email, audit forwarding, exports, imports, and long-running reports.
- Decide queue driver, retry, backoff, failed-job, and idempotency strategies.
- Decide scheduler ownership for pruning, cache warming, reports, and health checks.
- Decide health check endpoints and deployment smoke tests.

## 15. Security, Privacy, and Deployment Readiness

- Decide production configuration requirements for HTTPS, secure cookies, trusted proxies, CORS, CSP, and session domain.
- Decide secrets management and rotation policy for app keys, mail credentials, database credentials, and third-party tokens.
- Decide whether content security policy nonces are needed for Inertia/Vite assets.
- Decide how account enumeration is prevented in auth flows.
- Decide whether admin areas require IP allowlisting or step-up authentication.
- Decide whether user impersonation is needed and, if so, how it is permissioned and audited.
- Decide backup, restore, disaster recovery, and database migration rollback expectations.
- Decide deployment targets, runtime versions, queue workers, scheduler processes, and storage configuration.
- Decide whether security headers and vulnerability scans are required before release.
- Decide incident response ownership for compromised admin accounts or permission drift.
