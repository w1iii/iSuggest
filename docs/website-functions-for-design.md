# Employee Suggestion Box — Website Functions & Design Reference

> A Material Design (Google Design System) reference for the Employee Suggestion Box application.
> Use this document to guide UI/UX design: layout, components, states, and user flows.

---

## 1. User Roles & Permission Map

| Role | Access Level | Key Actions |
|------|-------------|-------------|
| **Guest** | Read-only, public | Browse suggestions, view details |
| **Employee** | Authenticated user | Submit, comment, vote, edit own content |
| **Moderator** | Elevated privileges | Moderate content, change status, delete comments |
| **Admin** | Full system access | Manage users, categories, dashboard, settings |

---

## 2. Feature Inventory

### 2.1 Authentication (Phase 3)

| Function | Description | Auth Required | Design Notes |
|----------|-------------|---------------|--------------|
| Register | Create account (name, email, password) | No | Form with validation, role defaults to "employee" |
| Login | Email + password → get token | No | Form, redirect to previous page after login |
| Logout | Revoke session | Yes | Button in user menu |
| Get Current User | Fetch authenticated user profile | Yes | Used by navbar, profile page |

**User flows:**
- Guest → Login → Return to previous page
- Guest → Register → Auto-login → Redirect to home
- Navigate to auth-required page → Redirect to Login → After login → Redirect back

### 2.2 Core Suggestion Features (Phases 4, 10)

#### Suggestion List (Index) — `/suggestions`
| UI Element | Description | States |
|-----------|-------------|--------|
| **Filter Bar** | Status dropdown, Category dropdown, Search input, Sort selector | Default, Active filter, No results |
| **Suggestion Cards** | Grid of cards showing title, author, status badge, vote count, comment count, category, date | Loading (skeleton), Loaded, Empty, Error |
| **Pagination** | Page navigation at bottom | First page, Middle pages, Last page, Single page |
| **Responsive Grid** | 1 column (mobile) / 2 columns (tablet) / 3 columns (desktop) | — |

#### Suggestion Card Component
| Element | Description |
|---------|-------------|
| Category badge | Small colored chip (see Category colors in §2.4) |
| Status badge | Colored chip: Pending=yellow, Under Review=blue, Approved=green, Declined=red, Implemented=purple |
| Title | Link to detail page (truncated if long) |
| Author | Display name or "Anonymous" |
| Vote area | ▲ Upvote count / ▼ Downvote count (inline) |
| Comment count | Icon + count |
| Date | Relative timestamp ("2 days ago") |

#### Suggestion Create — `/suggestions/create`
| Field | Type | Validation |
|-------|------|-----------|
| Title | Text input | Required, min 5 chars, max 255 |
| Description | Textarea | Required, min 20 chars, max 10,000 |
| Category | Dropdown select | Optional, populated from API |
| Anonymous submission | Checkbox/toggle | Boolean |

**Auth guard:** Must be logged in. Redirect to login if not authenticated.

#### Suggestion Detail (Show) — `/suggestions/:id`
| Section | Content |
|---------|---------|
| Header | Title, author, category badge, status badge |
| Description | Full text body |
| Vote section | ▲ Upvote (count) — ▼ Downvote (count), highlighted if user voted |
| Action bar | Edit/Delete buttons (owner or moderator+), Status change dropdown (moderator+) |
| Comment section | List of comments + comment form |

### 2.3 Categories (Phase 5)

| Function | Description | Auth |
|----------|-------------|------|
| List categories | Returns all categories with suggestion counts | Public |
| Show category | Single category with recent suggestions | Public |
| Create category | Admin-only form | Admin |
| Update category | Edit name, slug, description, icon, color | Admin |
| Delete category | With confirmation, reassign suggestions | Admin |

#### Default Categories
| Name | Hex Color | Icon |
|------|-----------|------|
| Bug Report | `#ef4444` (red) | bug-report |
| Feature Request | `#8b5cf6` (purple) | feature-request |
| Improvement | `#3b82f6` (blue) | improvement |
| Workplace | `#10b981` (green) | workplace |
| Process | `#f59e0b` (amber) | process |
| Other | `#6b7280` (gray) | other |

### 2.4 Comments (Phases 6, 11)

| Function | Description | Auth |
|----------|-------------|------|
| List comments | Reverse chronological, on suggestion detail page | Public |
| Add comment | Textarea + Submit, appears optimistically | Required |
| Delete comment | Trash icon, visible to author or moderator+ | Required |

**Comment display:** Avatar + name + relative date + body text.

### 2.5 Voting (Phases 6, 11)

| Function | Description |
|----------|-------------|
| Upvote | ▲ arrow, increments upvote count |
| Downvote | ▼ arrow, increments downvote count |
| Toggle | Click same vote again → remove vote |
| Switch | Click opposite vote → switch vote |
| Auth gate | Not authenticated → redirect to login |

**UX behaviors:**
- Optimistic UI: count updates immediately, reverts on API error
- Highlighted state: user's active vote is filled/colored
- Available on both card (index) and detail page

### 2.6 Status Workflow & Moderation (Phases 7, 11)

#### Status Lifecycle
```
Pending → Under Review → Approved → Implemented
Pending → Declined
Under Review → Declined
Approved → Under Review
```

#### Status Badge Colors
| Status | Color |
|--------|-------|
| Pending | Yellow/Amber |
| Under Review | Blue |
| Approved | Green |
| Declined | Red |
| Implemented | Purple |

#### Moderator Functions
| Function | Description |
|----------|-------------|
| Change status | Dropdown on suggestion detail — valid transitions enforced |
| Pending queue | Tabbed view of all pending suggestions |
| Quick actions | Approve/Decline/Mark Under Review from list |
| Bulk actions | Select multiple → batch status change |

### 2.7 Admin Dashboard (Phases 8, 11)

#### Stats Overview
| Stat Card | Description |
|-----------|-------------|
| Total Suggestions | Count |
| Pending Suggestions | Count (needs attention) |
| Total Users | Count |
| Total Comments | Count |

#### Charts
| Chart Type | Data |
|-----------|------|
| Bar/Pie | Suggestions by status |
| Bar/Pie | Suggestions by category |

#### Lists
| Widget | Content |
|--------|---------|
| Recent Suggestions | Latest 5 with quick action buttons |
| Top Contributors | Users with most suggestions |

#### User Management
| Feature | Description |
|---------|-------------|
| Users table | Name, email, role badge, department, suggestion count, joined date |
| Search | Filter by name or email |
| Edit user | Modal/inline edit: name, email, role, department |
| Delete user | Confirmation dialog, reassign suggestions to admin |
| Self-delete prevention | Cannot delete own admin account |

#### Category Management
| Feature | Description |
|---------|-------------|
| Category list | Name, slug, color swatch, suggestion count |
| Create/Edit | Modal form: name, description, icon, color picker |
| Delete | Confirmation dialog |

### 2.8 Notifications (Phase 12)

#### Event Triggers
| Event | Recipient |
|-------|-----------|
| Suggestion status changed | Suggestion author |
| New comment on suggestion | Suggestion author (unless self-comment) |

#### Notification Bell (Navbar)
| Element | Description |
|---------|-------------|
| Bell icon | Shows when authenticated |
| Unread badge | Red dot/count on bell |
| Dropdown | Last 10 notifications |
| Notification item | Message + relative time, click navigates to suggestion |
| Mark as read | On click, or "Mark all read" button |

#### Notifications API
| Endpoint | Purpose |
|----------|---------|
| `GET /api/notifications` | List user's notifications |
| `POST /api/notifications/{id}/read` | Mark one as read |
| `POST /api/notifications/read-all` | Mark all as read |
| `GET /api/notifications/unread-count` | Poll for badge count |

### 2.9 Profile Page (Phase 11 — Optional)

| Feature | Description |
|---------|-------------|
| My suggestions | List of user's submitted suggestions |
| Edit profile | Change name, avatar, department |
| Activity view | Vote and comment history |

---

## 3. Navigation Structure

### 3.1 Navbar (AppLayout)
| Item | Visible To | Link |
|------|-----------|------|
| Logo / Brand | All | `/` |
| Browse Suggestions | All | `/suggestions` |
| Submit Suggestion | Authenticated | `/suggestions/create` |
| Notification Bell | Authenticated | Dropdown |
| Login | Guest | `/login` |
| Register | Guest | `/register` |
| User Menu (name + avatar) | Authenticated | Dropdown: Profile, Logout |
| Admin Link | Admin | `/admin` |

### 3.2 Admin Sidebar
| Item | Link |
|------|------|
| Dashboard | `/admin` |
| Users | `/admin/users` |
| Suggestions (all) | `/admin/suggestions` — optional |
| Categories | `/admin/categories` — inline on admin page |

### 3.3 Route Map

| Path | View | Auth | Role |
|------|------|------|------|
| `/` | Home (landing/intro) | — | — |
| `/login` | Login form | Guest only | — |
| `/register` | Register form | Guest only | — |
| `/suggestions` | Suggestion list (index) | — | — |
| `/suggestions/create` | Create suggestion | Required | employee+ |
| `/suggestions/:id` | Suggestion detail | — | — |
| `/admin` | Admin dashboard | Required | admin |
| `/admin/users` | User management | Required | admin |

---

## 4. UI Component Library

### 4.1 Base Components (Material Design aligned)

| Component | Variants | Notes |
|-----------|----------|-------|
| `BaseButton` | Primary, Secondary, Danger, Ghost (text) | Ripple effect, disabled state |
| `BaseInput` | Default, Error | Label always visible, validation error below |
| `BaseTextarea` | Default, Error | Auto-grow, char count optional |
| `BaseSelect` | Default, Error | Native or custom dropdown |
| `BaseBadge` | Colored chips | Used for status, categories |
| `BasePagination` | Prev/Next + page numbers | Responsive: truncate on mobile |
| `BaseModal` | Overlay with close | Title, body, actions. Click-outside to close |
| `BaseAlert` | Success, Error, Info, Warning | Auto-dismiss optional |
| `BaseCard` | Default, Clickable, Elevated | Used for suggestion cards |
| `BaseDropdown` | Menu items | For user menu, filter sort |
| `BaseAvatar` | Image or initials fallback | 32px (navbar), 40px (comments) |
| `BaseSkeleton` | Loading placeholder | Card, text, avatar variants |
| `BaseEmptyState` | Icon + message + CTA | When no results / no data |

### 4.2 Empty States

| Page | Empty State Message |
|------|--------------------|
| Suggestion index (no filters) | "No suggestions yet. Be the first!" + CTA |
| Suggestion index (with filters) | "No suggestions match your filters" |
| Comment section | "No comments yet. Start the discussion." |
| Notification dropdown | "No new notifications" |
| Admin dashboard (no data) | "No data available" |

### 4.3 Loading States

| Component | Loading Pattern |
|-----------|----------------|
| Suggestion list | 3–6 skeleton cards in grid layout |
| Suggestion detail | Skeleton page: title bar, body blocks, sidebar |
| Comment section | 2–3 skeleton comment rows |
| Admin dashboard | Skeleton stat cards + chart placeholders |
| Pagination | Disabled prev/next during loading |

---

## 5. Design Tokens (Material 3)

### 5.1 Color Palette (Recommended)

| Token | Value (Material 3) | Usage |
|-------|--------------------|-------|
| Primary | `#1976D2` (Blue 700) | Navbar, buttons, links |
| Primary Container | `#BBDEFB` (Blue 100) | Selected state, light backgrounds |
| Secondary | `#388E3C` (Green 700) | Success states, approved badge |
| Error | `#D32F2F` (Red 700) | Danger buttons, declined badge, errors |
| Surface | `#FFFFFF` / `#F5F5F5` | Card backgrounds |
| Background | `#FAFAFA` | Page background |
| On Primary | `#FFFFFF` | Text on primary |
| On Surface | `#212121` | Primary text |
| On Surface Variant | `#757575` | Secondary text, placeholders |

### 5.2 Typography (Material 3 Type Scale)

| Style | Size | Weight | Usage |
|-------|------|--------|-------|
| Headline Large | 32px | 400 | Page titles |
| Headline Medium | 28px | 400 | Section headers |
| Title Large | 22px | 500 | Card titles |
| Title Medium | 16px | 500 | Subheaders, modal titles |
| Body Large | 16px | 400 | Suggestion description |
| Body Medium | 14px | 400 | General body text |
| Label Large | 14px | 500 | Button text |
| Label Medium | 12px | 500 | Badge labels, timestamps |

### 5.3 Elevation (Shadows)

| Level | Value | Usage |
|-------|-------|-------|
| 0 | None | Modals, dialogs |
| 1 | `0 1px 2px rgba(0,0,0,.08)` | Cards resting state |
| 2 | `0 2px 4px rgba(0,0,0,.1)` | Cards hover, search bar |
| 3 | `0 4px 8px rgba(0,0,0,.12)` | Navbar, dropdowns |
| 4 | `0 8px 16px rgba(0,0,0,.14)` | Modals dialogs |

### 5.4 Shape (Border Radius)

| Token | Value | Usage |
|-------|-------|-------|
| Corner None | 0px | Navbar, full-width elements |
| Corner Small | 4px | Inputs, buttons |
| Corner Medium | 8px | Cards |
| Corner Large | 12px | Modals, dialogs |
| Corner Full | 50% | Avatars, circular icons |

---

## 6. Responsive Breakpoints

| Breakpoint | Width | Layout |
|------------|-------|--------|
| Mobile | < 640px | Single column, bottom nav |
| Tablet | 640px – 1024px | 2-column grid, sidebar collapses |
| Desktop | > 1024px | 3-column grid, full sidebar |

---

## 7. UX States Per Screen

### Suggestion Index (`/suggestions`)
1. **Loading** — Skeleton grid (6 cards)
2. **Loaded** — Cards with data
3. **Empty** — No results illustration + suggestion to adjust filters
4. **Error** — "Something went wrong" + Retry button

### Suggestion Detail (`/suggestions/:id`)
1. **Loading** — Skeleton layout
2. **Loaded** — Full content
3. **Not Found** — 404 state
4. **Error** — Retry button

### Suggestion Create (`/suggestions/create`)
1. **Dirty form** — Submit enabled
2. **Submitting** — Button shows spinner, fields disabled
3. **Validation error** — Inline field errors
4. **Success** — Redirect to new suggestion detail
5. **Server error** — Alert at top of form

### Admin Dashboard (`/admin`)
1. **Loading** — Skeleton stat cards + chart placeholders
2. **Loaded** — Stats, charts, recent lists
3. **Empty** — "No data" for new installations
4. **Error** — Retry on failed load

---

## 8. Interaction Patterns

| Pattern | Behavior |
|---------|----------|
| Vote click (unauthenticated) | Redirect to login, return after auth |
| Vote toggle | Click again to remove, click opposite to switch |
| Optimistic vote | Count updates immediately, reverts on error |
| Comment submit | Appears immediately (optimistic), error shows alert |
| Status change | Dropdown selection → API call → badge updates |
| Delete action | Confirmation dialog before deletion |
| Search | Debounced (300ms) on keypress |
| Navigation guard | Unsaved form → warn before leaving |
| Toast feedback | Success/error messages auto-dismiss after 3s |

---

## 9. Error & Edge Cases

| Scenario | Handling |
|----------|----------|
| API 401 (token expired) | Auto-redirect to login |
| API 403 (unauthorized) | Show "You don't have permission" |
| API 422 (validation) | Inline field errors |
| API 429 (rate limit) | "Too many requests, try later" |
| API 500 (server error) | "Something went wrong" + retry |
| Network offline | Detect offline state, show banner |
| Long suggestion titles | Truncate with ellipsis (2 lines on card) |
| Long descriptions | Full on detail, truncated on card list |

---

## 10. Key User Flows

### Flow 1: Guest browses → Submits suggestion
```
Home → Browse suggestions (scrolls/paginates)
     → Clicks suggestion → Reads detail
     → Clicks "Submit Suggestion" → Redirected to Login
     → Login/Register → Redirected to Create form
     → Fills form → Submits → Redirected to new suggestion
```

### Flow 2: Employee votes + comments
```
Suggestion list → Clicks vote (updates instantly)
               → Clicks suggestion → Reads detail
               → Votes again (toggle)
               → Scrolls to comments → Reads
               → Types comment → Submits → Appears instantly
```

### Flow 3: Moderator reviews pending suggestions
```
Login → Admin area → Pending tab
      → Reviews suggestion → Opens detail
      → Changes status to "Under Review" or "Approved"
      → Suggestion author gets notification
```

### Flow 4: Admin manages system
```
Login → Admin Dashboard → Sees stats
     → Users → Searches user → Edits role
     → Categories → Creates new category
     → Moderation queue → Batch approves
```

---

> **Next steps for design:**
> 1. Create Material 3 theme (colors, typography, shape tokens)
> 2. Design high-fidelity mockups for each screen in specified states
> 3. Build component library in Figma / similar tool
> 4. Prototype key user flows (especially voting, moderation)
> 5. Hand off with design tokens and component specs
