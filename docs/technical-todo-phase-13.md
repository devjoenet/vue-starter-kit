# Technical To-Do: Phase 13 Accessibility and Focus Regression Coverage

Phase goal: add dedicated keyboard and focus regression coverage on high-identity auth, admin, and settings surfaces while preserving the current dashboard composition.

## Planning and Inventory

- Confirm the high-identity surface list: welcome, login, registration, password reset, email verification, two-factor challenge, settings profile, settings password, settings two-factor, admin dashboard, admin users, admin roles, admin permissions, and admin audit logs.
- Map each surface to required keyboard paths, focusable controls, landmark expectations, and destructive-action flows.
- Document the accepted dashboard layout so tests can protect stability without asserting brittle visual details.
- Identify surfaces that use dialogs, menus, popovers, selects, tabs, and table controls requiring Reka-UI keyboard behavior coverage.

## Browser Test Work

- Create or extend `tests/Browser/Workspace/WorkspaceAccessibilityBrowserTest.php` with keyboard navigation scenarios for auth, admin, and settings pages.
- Add browser helpers for asserting visible focus, tab order progression, escape-to-close behavior, and focus return after dialogs.
- Add tests for admin index keyboard flows: search field, filter controls, sort headers, pagination links, row actions, and mobile controls where applicable.
- Add tests for settings flows: profile update, password update, two-factor setup, two-factor recovery codes, and delete-profile confirmation.
- Add tests for auth flows: login, registration, forgot password, reset password, confirm password, verify email, and two-factor challenge.
- Add tests for destructive confirmation dialogs to assert initial focus, cancel focus return, submit disabled/loading behavior, and escape handling.
- Add tests for admin assignment tables to assert checkbox keyboard toggling and sequential save feedback remains perceivable.

## Component and Composable Work

- Review `resources/js/components/DeleteConfirmationDialog.vue` for focus trapping, initial focus, escape handling, and focus return support.
- Review admin table components for `scope`, `aria-sort`, accessible names, and keyboard-reachable actions.
- Review `resources/js/components/admin/AuditLogFiltersCard.vue` for label associations, field descriptions, and reset/apply keyboard flow.
- Review `resources/js/components/UserIdentityFields.vue` and settings forms for field labels, autocomplete, error references, and helper text.
- Add or adjust shared composables if repeated focus management logic emerges from browser tests.
- Ensure toast updates and sequential-save feedback use accessible live regions through the existing toast/feed system.

## Backend and Route Support

- Ensure browser tests can seed deterministic users, roles, permissions, permission groups, and audit logs.
- Add factories or test helpers only where existing seeders are insufficient for deterministic browser assertions.
- Avoid changing dashboard data contracts unless a test exposes an accessibility-critical missing prop.
- Keep authorization request logic in form requests and policies rather than test-only controller branches.

## Guardrail and Quality Work

- Extend frontend guardrail tests to reject inaccessible admin table headers, unlabeled controls, or hardcoded route URLs if practical.
- Ensure any new shared testing helpers are documented in `tests/Pest.php` or a focused support file.
- Run the existing Pest suite subset for browser, accessibility, admin, auth, settings, architecture, and performance budgets.
- Update planning documentation with any newly discovered accessibility standards that become durable requirements.
