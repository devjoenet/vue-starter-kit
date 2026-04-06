# Agent Index

This indexes the actual `.ai/` and `.agents/` directories in this repository. When a file is a skill or guideline, the note explains what it tends to generate as code or how it steers implementation decisions. Example, reference, placeholder, license, and macOS metadata files are marked as such.

## Role Split

| Area | Role |
| --- | --- |
| `.ai/` | Project-specific control plane: hard rules, roadmap, design context, local workflows, templates, examples, and one focused helper agent. |
| `.agents/` | Execution layer: design, frontend craft, Laravel workflow, testing, Tailwind, Fortify, Inertia, Wayfinder, and review/audit helpers. |

## `.ai/`

### Core Files

| Path | Kind | What It Yields or Steers |
| --- | --- | --- |
| `.ai/agents/laravel-simplifier.md` | Helper agent | Behavior-preserving Laravel/PHP refactors: flatter control flow, clearer naming, fewer nested ternaries, fewer redundant comments, and more explicit code. |
| `.ai/artifacts/.gitkeep` | Placeholder | Reserves `.ai/artifacts/` for AI-generated plans, audits, summaries, and investigation notes. |
| `.ai/commands/reflect.md` | Command | Produces proposed guideline updates after reviewing session mistakes and reusable patterns, then requires approval and `php artisan boost:update`. |

### Guidelines

| Path | Kind | What It Yields or Steers |
| --- | --- | --- |
| `.ai/guidelines/0-project-rules.md` | Hard override | Pushes work toward strict docblock formatting, module-local PHP classes under `app/Modules/*`, Eloquent over avoidable `DB::` calls, named exceptions, no GitHub workflows yet, and a required markdown close-out format. |
| `.ai/guidelines/00-standards.blade.php` | Assembler | Builds the authoritative runtime standards from the local stub files. It does not scaffold code directly; it turns the stubs into the rules that govern edits and finalization. |
| `.ai/guidelines/design-context.md` | Design baseline | Pushes UI decisions toward the Southeast Code visual system: `Welcome.vue` as the shell baseline, tinted neutrals, restrained glow, brand teal plus action cyan, and explicit avoidance of generic admin/dashboard furniture. |
| `.ai/guidelines/laravel/core.blade.php` | Laravel override | Forces Form Requests for validation, forbids database queries inside API Resources, and reinforces event-driven, DTO/action-based architecture. |
| `.ai/guidelines/phpunit/core.blade.php` | Testing override | Pushes tests toward mirrored directory structure, `UnitTestCase` and `FunctionalTestCase`, strict AAA+A formatting, `CoversClass`, `resolve()`-based DI, and HTTP-level controller coverage. |
| `.ai/guidelines/pint/core.blade.php` | Formatter override | Steers finalization toward `composer style:fix` rather than ad hoc Pint commands. |
| `.ai/guidelines/reports/system-audit.md` | Audit ledger | Influences prioritization by recording resolved and open findings, especially dashboard drift and missing CI parity. |

### Guideline Stubs

| Path | Kind | What It Yields or Steers |
| --- | --- | --- |
| `.ai/guidelines/stubs/architecture.stub` | Source stub | Defines target slice layout, module boundaries, action naming, DTO behavior, contract usage, and exception handling rules. |
| `.ai/guidelines/stubs/behavior.stub` | Source stub | Defines composer-script usage, artifact placement, communication behavior, and mandatory task finalization. |
| `.ai/guidelines/stubs/code-change-summary.stub` | Example stub | Shows the required close-out structure: phase headline, notable code changes, and validation commands. |
| `.ai/guidelines/stubs/compliance.stub` | Source stub | Forces reading relevant skills before editing classes/tests, requires an explicit `[Compliance Verified: ...]` acknowledgment before writes, and enforces verification locks before completion. |
| `.ai/guidelines/stubs/quality.stub` | Source stub | Pushes comments toward only non-obvious decisions, complex algorithms, and workarounds. |

### Skills

| Path | Kind | What It Yields or Steers |
| --- | --- | --- |
| `.ai/skills/create-dto-action/SKILL.md` | Skill | Yields slice-local actions with public static `handle()`, DTOs that carry all inputs, `fromRequest()`-style provisioning, and minimal querying inside action bodies. |
| `.ai/skills/create-dto-action/examples/Action.php` | Example | Shows an action class expecting a DTO-resolved model and exposing `handle()` plus optional helper methods. |
| `.ai/skills/create-dto-action/examples/DTO.php` | Example | Shows a readonly DTO with constructor promotion and a request factory that resolves dependencies before the action runs. |
| `.ai/skills/format-phpunit-tests/SKILL.md` | Skill | Yields mirrored test locations, base-class choices, AAA+A comments, generator-based data providers, and `CoversClass`. |
| `.ai/skills/format-phpunit-tests/examples/FunctionalTest.php` | Example | Demonstrates the expected functional-test structure and spacing. |
| `.ai/skills/format-phpunit-tests/examples/IntegrationTest.php` | Example | Demonstrates the expected integration-test structure, including `test_it_integrates()`. |
| `.ai/skills/scaffold-module/SKILL.md` | Skill | Yields `app/Modules/{Domain}` plus the corresponding HTTP transport layer and nudges route registration plus cross-module isolation checks. |
| `.ai/skills/scaffold-module/examples/directory-tree.txt` | Example | Shows the target module and transport directory tree. |
| `.ai/skills/task-finalization/SKILL.md` | Skill | Steers end-of-task verification toward `composer style:fix`, `composer phpstan`, `composer rector`, `composer test:type-coverage`, and the test suite. |

## `.agents/`

### Design, Frontend, and UX Skills

| Path | Kind | What It Yields or Steers |
| --- | --- | --- |
| `.agents/skills/adapt/SKILL.md` | Skill | Reflows layouts, interaction models, navigation, and content priority for mobile, tablet, desktop, print, or email instead of just shrinking the existing UI. |
| `.agents/skills/animate/SKILL.md` | Skill | Adds purposeful entrances, feedback states, smoother transitions, and reduced-motion-safe animation plans after gathering enough design context. |
| `.agents/skills/audit/SKILL.md` | Skill | Produces a prioritized interface audit covering accessibility, performance, theming, responsiveness, anti-patterns, positive findings, and follow-up commands. |
| `.agents/skills/bolder/SKILL.md` | Skill | Pushes bland interfaces toward stronger typography, more intentional scale, richer color, sharper hierarchy, and bigger focal moments without generic neon sludge. |
| `.agents/skills/clarify/SKILL.md` | Skill | Produces clearer labels, button text, error messages, empty states, confirmations, and microcopy with more specific, active, helpful language. |
| `.agents/skills/colorize/SKILL.md` | Skill | Adds purposeful accent usage, semantic state colors, tinted surfaces, and better hierarchy in monochrome UI. |
| `.agents/skills/critique/SKILL.md` | Skill | Produces an honest UX critique with anti-pattern detection, priority issues, concrete fixes, and pointed questions about hierarchy, affordance, structure, and tone. |
| `.agents/skills/delight/SKILL.md` | Skill | Adds subtle celebratory moments, personality in loading/success/empty states, and warmer micro-interactions without blocking users. |
| `.agents/skills/distill/SKILL.md` | Skill | Removes clutter: flatter hierarchy, fewer competing actions, less redundant copy, fewer decorative containers, and tighter progressive disclosure. |
| `.agents/skills/extract/SKILL.md` | Skill | Extracts reusable components, clearer props, shared design tokens, and repeated UI patterns into shared code. |
| `.agents/skills/frontend-design/SKILL.md` | Skill | Main high-design frontend driver. Steers work toward bold, authored, non-generic visual directions with strong typography, purposeful color, asymmetry, and anti-slop discipline. |
| `.agents/skills/frontend-design/reference/color-and-contrast.md` | Reference | Supports palette construction, OKLCH usage, dark mode, and contrast decisions. |
| `.agents/skills/frontend-design/reference/interaction-design.md` | Reference | Supports interaction, focus, form, loading, and control behavior decisions. |
| `.agents/skills/frontend-design/reference/motion-design.md` | Reference | Supports animation timing, easing, reduced motion, and motion hierarchy decisions. |
| `.agents/skills/frontend-design/reference/responsive-design.md` | Reference | Supports mobile-first and container-aware responsive decisions. |
| `.agents/skills/frontend-design/reference/spatial-design.md` | Reference | Supports layout rhythm, composition, spacing, and grid decisions. |
| `.agents/skills/frontend-design/reference/typography.md` | Reference | Supports font pairing, hierarchy, scales, and typography loading strategy. |
| `.agents/skills/frontend-design/reference/ux-writing.md` | Reference | Supports labels, errors, empty states, and concise interface writing. |
| `.agents/skills/harden/SKILL.md` | Skill | Produces overflow handling, error states, i18n-safe layouts, empty/loading states, permission handling, and more realistic edge-case behavior. |
| `.agents/skills/normalize/SKILL.md` | Skill | Replaces one-off components and values with project-standard tokens, patterns, and interaction behavior. |
| `.agents/skills/onboard/SKILL.md` | Skill | Produces welcome flows, contextual help, empty states, first-success paths, guided tours, and clearer time-to-value for new users. |
| `.agents/skills/optimize/SKILL.md` | Skill | Produces image optimization, bundle/code-splitting decisions, layout-thrash avoidance, GPU-safe animation, Core Web Vitals fixes, and lazy loading strategies. |
| `.agents/skills/overdrive/SKILL.md` | Skill | Produces high-ambition, contextual enhancements such as cinematic transitions, scroll-driven motion, virtualization, or advanced browser effects, but only after proposing options and getting approval. |
| `.agents/skills/polish/SKILL.md` | Skill | Adds spacing/alignment cleanup, typography consistency, missing interaction states, edge-case refinement, responsiveness cleanup, and final-pass finish. |
| `.agents/skills/quieter/SKILL.md` | Skill | Calms visually aggressive UI by reducing saturation, visual weight, motion intensity, and decorative noise while keeping the design intentional. |
| `.agents/skills/tailwindcss-development/SKILL.md` | Skill | Produces Tailwind v4-correct utility usage, grid/flex layout composition, dark-mode handling, gap-based spacing, and removal of deprecated v3 patterns. |
| `.agents/skills/teach-impeccable/SKILL.md` | Skill | Produces a `## Design Context` section in `AGENTS.md` after exploring the project and asking only the questions the codebase cannot answer. |
| `.agents/skills/ui-craft/SKILL.md` | Skill | Comprehensive design-engineering guide that routes UI work into build, animate, review, polish, or audit modes and pushes for craft-level execution. |
| `.agents/skills/ui-craft/references/accessibility.md` | Reference | Supports accessibility heuristics and interaction safety. |
| `.agents/skills/ui-craft/references/animation-orchestration.md` | Reference | Supports sequencing and choreography for multi-step animations. |
| `.agents/skills/ui-craft/references/animation.md` | Reference | Supports motion craft, easing, timing, and animation behavior. |
| `.agents/skills/ui-craft/references/color.md` | Reference | Supports theme construction, palette usage, and color hierarchy. |
| `.agents/skills/ui-craft/references/copy.md` | Reference | Supports microcopy and UX-writing decisions. |
| `.agents/skills/ui-craft/references/layout.md` | Reference | Supports composition, spacing, grid usage, and section structure. |
| `.agents/skills/ui-craft/references/modern-css.md` | Reference | Supports advanced CSS features such as view transitions and modern layout/motion tooling. |
| `.agents/skills/ui-craft/references/performance.md` | Reference | Supports animation/runtime performance and jank reduction. |
| `.agents/skills/ui-craft/references/responsive.md` | Reference | Supports responsive and adaptive UI behavior. |
| `.agents/skills/ui-craft/references/review.md` | Reference | Supports interface critique and review workflow. |
| `.agents/skills/ui-craft/references/sound.md` | Reference | Supports optional sound-design decisions in UI. |
| `.agents/skills/ui-craft/references/typography.md` | Reference | Supports type hierarchy, rhythm, and font usage. |

### Laravel and Application Workflow Skills

| Path | Kind | What It Yields or Steers |
| --- | --- | --- |
| `.agents/skills/fortify-development/SKILL.md` | Skill | Steers auth work toward Fortify config, routes, actions, contracts, 2FA, email verification, password reset, SPA session auth, and response overrides instead of inventing a second auth system. |
| `.agents/skills/inertia-vue-development/SKILL.md` | Skill | Produces correctly structured page components, `<Link>` navigation, `<Form>` or `useForm` usage, deferred props, prefetching, and idiomatic Inertia v3 patterns. |
| `.agents/skills/laravel-best-practices/SKILL.md` | Skill | Main backend Laravel rulebook. Pushes decisions toward consistency with nearby code, Eloquent/query efficiency, security, caching, Form Requests, queue and scheduling hygiene, thin controllers, migration discipline, and Laravel-native architecture. |
| `.agents/skills/laravel-best-practices/rules/*.md` | Rule set | Detailed rule files consulted by the main Laravel skill. Together they bias implementation toward performant queries, safe validation, sane job behavior, clean routing/controllers, better migrations, consistent style, and fewer avoidable Laravel mistakes. |
| `.agents/skills/pest-testing/SKILL.md` | Skill | Produces Pest test structure, browser/smoke/architecture testing patterns, dataset usage, better assertion choices, and a bias toward real Laravel testing primitives. |
| `.agents/skills/spatie-laravel-php-standards/SKILL.md` | Skill | Steers edits toward Laravel conventions, PSR-12, typed properties/returns, early returns, array validation syntax, and avoidance of unnecessary docblocks. |
| `.agents/skills/spatie-laravel-php-standards/references/spatie-laravel-php-guidelines.md` | Reference | Supplies the fuller wording for the style and formatting standards the Spatie skill applies. |
| `.agents/skills/wayfinder-development/SKILL.md` | Skill | Produces named imports from generated Wayfinder actions/routes, `.url()` and `.form()` usage, proper regeneration after route changes, and fewer hardcoded frontend URLs. |

### Metadata and Support Files

| Path | Kind | What It Yields or Steers |
| --- | --- | --- |
| `.ai/.DS_Store` | Metadata | macOS Finder metadata. No steering value. |
| `.ai/guidelines/.DS_Store` | Metadata | macOS Finder metadata. No steering value. |
| `.ai/skills/.DS_Store` | Metadata | macOS Finder metadata. No steering value. |
| `.ai/skills/create-dto-action/.DS_Store` | Metadata | macOS Finder metadata. No steering value. |
| `.agents/.DS_Store` | Metadata | macOS Finder metadata. No steering value. |
| `.agents/skills/.DS_Store` | Metadata | macOS Finder metadata. No steering value. |
| `.agents/skills/ui-craft/.DS_Store` | Metadata | macOS Finder metadata. No steering value. |
| `.ai/guidelines/stubs/.gitignore` | Support file | Keeps stub-local generated noise out of version control. |
| `.agents/skills/spatie-laravel-php-standards/license.txt` | License | Upstream license text for the Spatie standards material. |

## Practical Shortcut

| Task Type | Highest-Pressure Files |
| --- | --- |
| PHP architecture or Laravel workflow | `.ai/guidelines/0-project-rules.md`, `.ai/skills/create-dto-action/SKILL.md`, `.agents/skills/laravel-best-practices/SKILL.md`, `.agents/skills/spatie-laravel-php-standards/SKILL.md`, `.agents/skills/pest-testing/SKILL.md` |
| Frontend UI | `.ai/guidelines/design-context.md`, `.agents/skills/frontend-design/SKILL.md`, `.agents/skills/ui-craft/SKILL.md`, `.agents/skills/tailwindcss-development/SKILL.md`, plus whichever of `normalize`, `distill`, `colorize`, `clarify`, `polish`, `animate`, or `harden` best matches the problem |
