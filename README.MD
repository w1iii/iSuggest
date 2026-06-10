# iSuggest — Employee Suggestion Box

A full-stack employee suggestion management system built with Laravel 13 + Vue 3.

## Project Setup

### Prerequisites

- PHP ^8.3
- Composer
- Node.js ^20
- MySQL 8+ (or SQLite for testing)
- npm

### Installation

```bash
# Clone the repository
git clone <repo-url>
cd employee-suggestion-box

# Backend setup
cd backend
composer install
cp .env.example .env
php artisan key:generate

# Frontend setup
cd ../frontend
npm install
```

### Environment Setup

Configure `.env` in `backend/`:

```env
APP_NAME=iSuggest
APP_URL=http://localhost:8000
FRONTEND_URL=http://localhost:5173

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=employee_suggestion_box
DB_USERNAME=root
DB_PASSWORD=isuggest

SANCTUM_STATEFUL_DOMAINS=localhost:5173
SESSION_DRIVER=file
```

### Database Configuration

```bash
# Create database and run migrations
mysql -u root -p -e "CREATE DATABASE employee_suggestion_box"
php artisan migrate

# Seed demo data
php artisan db:seed

# Create storage link for profile images
php artisan storage:link
```

### Running the App

```bash
# Terminal 1 - Backend API
cd backend
php artisan serve

# Terminal 2 - Queue worker (for emails)
php artisan queue:listen

# Terminal 3 - Frontend dev server
cd frontend
npm run dev
```

Access frontend at `http://localhost:5173`, API at `http://localhost:8000/api/v1`.

### Default Accounts (seeded)

| Role | Email | Password |
|------|-------|----------|
| Super Administrator | superadmin@example.com | 123 |
| Administrator | admin@example.com | 123 |
| Employee | employee@example.com | 123 |

## Features Implemented

### Authentication & Authorization
- Laravel Sanctum SPA authentication
- Role-based access control (Super Administrator, Administrator, Employee)
- Login/register/logout with rate limiting (6 req/min)
- Admin creates employee accounts (triggers welcome email)

### Suggestions CRUD
- Employees create, read, update, delete own suggestions
- Title, description, category (4 categories), status (5 statuses)
- Validation via Form Requests
- Authorization gates — employees cannot modify others' suggestions

### Admin Dashboard
- Stats cards (total suggestions, by status, by category)
- Recent activity feed
- Category insights bar chart
- Top contributors leaderboard
- Trends by period (weekly/monthly/quarterly/yearly)
- Download analytics PDF report

### Admin Suggestion Management
- Kanban board with drag-drop between status columns
- Optimistic UI updates on status change
- Admin remarks field
- Filter by status

### Employee Management
- Admin employee list with search and status filter
- Create employee with welcome email notification

### User Profile
- View/edit profile (name, bio, title, field)
- Profile image upload
- User stats on profile page

### Analytics
- Trends chart, category distribution donut
- Strategic insights display
- Top ideas table
- PDF report generation via dompdf

### Notifications
- Welcome email to new employees
- Queue-based email dispatch (database driver)

### Testing
- **Backend**: 26 PHPUnit feature tests (auth, suggestions, admin)
- **Frontend**: 13 Vitest tests (auth store, SuggestForm component)

## Assumptions Made

1. **MySQL as primary database** — SQLite used only for testing; production assumes MySQL
2. **Single-company tenant** — No multi-tenant support; all users belong to one organization
3. **Email via SMTP** — Mail configured via Gmail SMTP; assumes outbound SMTP available
4. **Queue-backed notifications** — Emails dispatched via database queue; assumes `queue:listen` running
5. **SPA-only frontend** — No SSR; assumes JavaScript enabled on client
6. **Admin sets status manually** — No auto-transition rules; admins move statuses on Kanban
7. **Categories are static** — Defined in config, no CRUD UI for categories
8. **No soft deletes** — Suggestions are hard-deleted
9. **Profile images served via storage link** — Requires `php artisan storage:link`
10. **Session-based file storage** — Sessions stored on filesystem, not Redis

## Challenges Encountered

### 1. Sanctum SPA vs Token Auth
Initial confusion mixing SPA session auth with token auth. Resolved by using Sanctum's SPA flow (`EnsureFrontendRequestsAreStateful` middleware) with CSRF cookie for web requests, while keeping Bearer token option for API clients.

### 2. Role Middleware Bypass for Super Admin
Design decision: Super Administrator should have full access. Implemented by checking `$request->user()->role === 'Super Administrator'` in `RoleMiddleware` before enforcing the required role.

### 3. Kanban Drag-and-Drop with Optimistic Updates
Frontend challenge: updating suggestion status on drag while showing instant feedback. Solved with optimistic Pinia store updates that revert on API failure, plus `@vueuse/integrations` sortable logic.

### 4. Admin vs Employee Route Separation
Keeping employee routes (suggestions CRUD) and admin routes scoped correctly. Solved by separate controller namespaces (`Admin\SuggestionController` vs employee controllers) and middleware groups in `routes/api.php`.

### 5. Profile Image Upload Handling
Mulipart form data for profile updates required `Content-Type: multipart/form-data` header and using FormData in Axios. The `updateProfile` action in the auth store handles this by conditionally appending the image file.

### 6. Test Database Isolation
PHPUnit tests use SQLite in-memory with `RefreshDatabase` trait. Ensuring test factories and seeders work across both SQLite and MySQL required care with enum column definitions (SQLite lacks native ENUM support — handled via string columns in migrations with `check` constraints for MySQL).
