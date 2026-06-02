
# Employee Suggestion Box — Build Phases

This directory contains the phased build plan for the Employee Suggestion Box application (Laravel + Vue 3).

## Phase Overview

| # | Phase | Focus | Est. Effort |
|---|-------|-------|-------------|
| 01 | [Planning & Architecture](01-planning-and-architecture.md) | Requirements, data model, API route map | 1 day |
| 02 | [Database Migrations & Models](02-database-migrations-and-models.md) | Migrations, Eloquent models, factories, seeders | 1–2 days |
| 03 | [Authentication & Authorization](03-authentication-and-authorization.md) | Sanctum, register/login, role middleware | 1 day |
| 04 | [API — Core Suggestions](04-api-core-suggestions.md) | Suggestions CRUD, search, filter, pagination | 1–2 days |
| 05 | [API — Categories](05-api-categories.md) | Category CRUD, integration with suggestions | 0.5 day |
| 06 | [API — Comments & Votes](06-api-comments-and-votes.md) | Comments, upvote/downvote, toggle logic | 1 day |
| 07 | [API — Status Workflow & Moderation](07-api-status-workflow-and-moderation.md) | Status transitions, moderation queue | 1 day |
| 08 | [API — Admin Dashboard](08-api-admin-dashboard.md) | Dashboard stats, user management | 1 day |
| 09 | [Frontend Foundation](09-frontend-foundation.md) | Vue Router, Axios client, Pinia stores, auth UI | 2 days |
| 10 | [Frontend — Suggestion Features](10-frontend-suggestion-features.md) | Suggestion list, create, detail, filters | 2–3 days |
| 11 | [Frontend — Social & Admin](11-frontend-social-and-admin.md) | Comments UI, voting UX, admin dashboard | 2 days |
| 12 | [Notifications](12-notifications.md) | Email + in-app notifications | 1–2 days |
| 13 | [Testing](13-testing.md) | PHPUnit, Vitest, component tests | 2 days |
| 14 | [Deployment & DevOps](14-deployment-and-devops.md) | Docker, CI/CD, production setup | 1–2 days |


## How to Use

Each phase document contains:
- **Goal** — what the phase accomplishes
- **Steps** — detailed implementation tasks with checkboxes
- **Commands** — relevant artisan/npm commands
- **Deliverables** — checklist of completion criteria

### Workflow

1. Start with Phase 1 and work through sequentially
2. Mark off checklist items as you complete them
3. Run tests after each phase before moving to the next
4. Commit code at the end of each phase

### Stack

- **Backend**: Laravel 12 + Sanctum + SQLite (dev) / PostgreSQL (prod)
- **Frontend**: Vue 3 (Composition API) + Pinia + Vue Router + Vite
- **Testing**: PHPUnit (backend) + Vitest (frontend)
- **Deployment**: Docker + GitHub Actions
