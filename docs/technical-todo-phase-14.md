# Technical To-Do: Phase 14 CI, Release Governance, and Automation

Phase goal: convert deferred governance work into explicit automation once deployment tooling and runner requirements are confirmed.

## Decisions Required Before Implementation

- Confirm the CI provider, runner OS, PHP version, Node version, database service, browser dependencies, and cache strategy.
- Confirm whether `.github` workflows are allowed; current planning says not to add them until explicit.
- Confirm which checks are blocking for pull requests and which are advisory.
- Confirm whether browser tests are blocking on every PR or only on protected branches/nightly runs.

## Workflow and Script Work

- Add CI workflow files only after the governance decision is explicit.
- Create separate jobs for Composer validation/install, npm install/build/typecheck/lint, Pest feature/unit tests, architecture tests, and browser tests.
- Add MySQL service configuration and migration/seed steps for backend tests.
- Add browser dependency installation for Laravel Dusk/Pest browser coverage if required by the selected runner.
- Cache Composer and npm dependencies without caching generated artifacts that can hide stale builds.
- Upload test logs, screenshots, browser console output, and coverage artifacts on failure.

## Composer and npm Quality Commands

- Standardize local scripts for PHP formatting, static analysis if adopted, Pest suites, frontend linting, type checking, and production builds.
- Add a documented `composer test` or equivalent orchestration command if the team wants one-command local verification.
- Verify generated Wayfinder/types files are refreshed before frontend type checks.
- Ensure asset budget checks run after production build, not development build.

## Pull Request Governance

- Add a PR template with summary, testing, screenshots, accessibility notes, risk notes, and rollback plan if repository governance allows it.
- Add issue templates for feature, bug, architecture decision, and accessibility regression reports if useful.
- Add CODEOWNERS only after module ownership is confirmed.
- Document required review roles for auth, IAM, audit, security, frontend system, and database changes.

## Release Readiness Work

- Document release checklist covering migrations, seeders, queues, scheduler, cache clears, permission sync, smoke tests, and rollback.
- Add deployment smoke tests for welcome, auth, settings, admin dashboard, admin users, and audit logs.
- Add health checks for database, cache, queue, mail configuration, and storage where applicable.
- Define versioning, changelog, and hotfix procedures.
