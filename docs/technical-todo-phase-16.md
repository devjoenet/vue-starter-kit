# Technical To-Do: Phase 16 Operational Hardening and Production Readiness

Phase goal: prepare the application for durable production operation across security, observability, data retention, and incident response.

## Security Configuration

- Document required production environment variables for app URL, session domain, secure cookies, trusted proxies, mail, queue, cache, database, and logging.
- Configure and test HTTPS-only cookies, same-site policy, CSRF behavior, trusted proxies, and secure headers.
- Decide and implement content security policy if required by deployment constraints.
- Review auth flows for account enumeration, throttling, password confirmation, and two-factor enforcement.
- Add step-up authentication for sensitive admin actions if required.
- Add or document admin IP allowlisting if required by the deployment environment.

## Observability

- Ensure request IDs are present in logs, slow-query entries, audit metadata, and user-facing support messages where appropriate.
- Standardize structured log fields for user ID, actor label, route name, module, action, request ID, and severity.
- Add operational metrics for auth failures, admin mutations, audit-log volume, queue failures, slow requests, and dashboard latency.
- Add alerts for failed jobs, migration failures, permission sync failures, high error rates, and suspicious auth activity.
- Add health checks for database, cache, queue, mail, storage, scheduler freshness, and app version.

## Data Retention and Privacy

- Implement audit-log retention and pruning after retention requirements are decided.
- Add anonymization or redaction actions for deleted users if privacy requirements demand it.
- Ensure sensitive audit fields are masked consistently before persistence.
- Add export and legal-hold procedures if compliance requirements demand them.
- Document backup, restore, disaster recovery, and verification drills.

## Queue and Scheduler

- Configure queue driver, workers, retries, backoff, timeouts, failed-job storage, and alerting.
- Move long-running exports, imports, email delivery, report generation, or external audit forwarding to queued jobs.
- Add scheduled tasks for pruning, cache warming, permission integrity checks, health checks, and report delivery.
- Add tests for job idempotency and retry-safe side effects.

## Deployment Readiness

- Document zero-downtime deployment steps, maintenance-mode fallback, migration order, cache clears, queue restarts, and rollback steps.
- Add smoke tests for public, auth, settings, admin, audit, and dashboard routes.
- Verify production builds respect frontend asset budgets.
- Verify config, route, view, and event caches work with module conventions.
- Add release notes and incident-response runbooks.
