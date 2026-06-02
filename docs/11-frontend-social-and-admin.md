# Phase 11: Frontend — Social Features & Admin Panel

## Goal
Build comment threads, voting UX on index cards, and the admin dashboard UI.

## Steps

### 11.1 Comment Section (Suggestion Show View)

- [ ] **Comment list**: reverse chronological, shows user avatar + name + relative date + body
- [ ] **Comment form**: textarea + submit button (auth required)
- [ ] **Delete comment**: only visible to comment author or moderator+
- [ ] **Optimistic updates**: comment appears immediately after submit
- [ ] **Loading state**: spinner while comments load

### 11.2 Vote UX

**On suggestion cards (index):**
- [ ] Upvote arrow (▲) and downvote arrow (▼) with counts
- [ ] Highlighted if user has voted
- [ ] Click requires auth — redirect to login if not authenticated
- [ ] Optimistic UI: count updates immediately, reverts on error
- [ ] Click same vote again → remove vote

**On suggestion detail:**
- [ ] Same as above but larger / more prominent
- [ ] Vote count animates on change

### 11.3 Admin Dashboard View

- [ ] **Stats cards**: total suggestions, pending, users, comments
- [ ] **Chart**: suggestions by status (bar or pie — use simple CSS or minimal chart lib)
- [ ] **Chart**: suggestions by category
- [ ] **Recent suggestions**: latest 5 with quick actions
- [ ] **Top contributors**: users with most suggestions

### 11.4 Admin — User Management

- [ ] **Users table**: name, email, role, department, suggestion count, joined date
- [ ] **Edit user**: modal/inline to change name, email, role, department
- [ ] **Delete user**: confirmation modal, warning about reassigning suggestions
- [ ] **Search**: filter users by name or email

### 11.5 Admin — Suggestion Moderation Queue

- [ ] **Pending tab**: all pending suggestions
- [ ] **Quick actions**: Approve, Decline, Mark Under Review (from list view)
- [ ] **Bulk actions**: select multiple → batch status change
- [ ] **Filters**: by status, category, date range

### 11.6 Admin — Category Management

- [ ] **Category list**: name, slug, color preview, suggestion count
- [ ] **Create/Edit modal**: name, description, icon, color picker or text input
- [ ] **Delete**: with confirmation, reassign/nullify suggestions

### 11.7 Profile Page (Optional)

- [ ] User can view their own suggestions
- [ ] User can change name, avatar, department
- [ ] Shows their vote/comment activity

## Deliverables
- [ ] Comment thread working with CRUD
- [ ] Voting UX with optimistic updates
- [ ] Admin dashboard with stats
- [ ] User management table
- [ ] Moderation queue for pending suggestions
- [ ] Category management
- [ ] Responsive admin layout
