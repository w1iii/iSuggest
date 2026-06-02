# Phase 1: Planning & Architecture

## Goal
Establish clear requirements, data model, and architectural decisions before writing code.

## Steps

### 1.1 Define Feature Scope
- [ ] **Core features**: Submit suggestion, browse suggestions, search/filter
- [ ] **Social features**: Comment on suggestions, upvote/downvote
- [ ] **Moderation**: Status workflow (Pending → Under Review → Approved/Declined/Implemented)
- [ ] **Admin**: Manage suggestions, users, categories
- [ ] **Notifications**: Email when status changes, new comments (optional: in-app)

### 1.2 Define User Roles
| Role | Permissions |
|------|-------------|
| Guest | View public suggestions only |
| Employee | Submit, comment, vote on suggestions |
| Moderator | Edit any suggestion, change status, moderate comments |
| Admin | Full access: manage users, categories, settings |

### 1.3 Data Model

```
User (extends Authenticatable)
  - id, name, email, password, role (enum: employee/moderator/admin), avatar, department

Suggestion
  - id, user_id (FK), title, description, category_id (FK), status (enum), anonymous (bool), created_at, updated_at

Category
  - id, name, slug, description, icon, color

Comment
  - id, suggestion_id (FK), user_id (FK), body, created_at, updated_at

Vote
  - id, suggestion_id (FK), user_id (FK), vote (enum: up/down)
  - Unique constraint: (suggestion_id, user_id)
```

### 1.4 Architecture Decisions
- **Backend**: Laravel 12 with Sanctum for API auth
- **Frontend**: Vue 3 + Pinia + Vue Router + Vite
- **Database**: SQLite (dev), PostgreSQL (production-ready migrations)
- **API**: RESTful JSON API with resource endpoints
- **Auth**: Token-based (Sanctum) with ability middleware for roles

### 1.5 API Route Map

```
GET    /api/suggestions          # List (paginated, filterable)
POST   /api/suggestions          # Create
GET    /api/suggestions/{id}     # Show
PUT    /api/suggestions/{id}     # Update (owner/moderator)
DELETE /api/suggestions/{id}     # Delete (owner/moderator)

GET    /api/categories           # List
POST   /api/categories           # Create (admin)
PUT    /api/categories/{id}      # Update (admin)
DELETE /api/categories/{id}      # Delete (admin)

POST   /api/suggestions/{id}/comments   # Add comment
DELETE /api/comments/{id}               # Delete comment

POST   /api/suggestions/{id}/vote       # Vote (up/down)
DELETE /api/suggestions/{id}/vote       # Remove vote

POST   /api/register
POST   /api/login
POST   /api/logout
GET    /api/user               # Current user
```

## Deliverables
- [ ] Feature specification document ✅ (this file)
- [ ] Finalized data model
- [ ] API route map
- [ ] Architecture decisions recorded
