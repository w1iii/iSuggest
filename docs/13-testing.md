# Phase 13: Testing

## Goal
Achieve test coverage for all critical backend and frontend paths.

## Steps

### 13.1 Backend — PHPUnit Tests

```bash
php artisan make:test AuthTest
php artisan make:test SuggestionTest
php artisan make:test CommentTest
php artisan make:test VoteTest
php artisan make:test ModerateTest
php artisan make:test AdminTest
```

#### AuthTest
- [ ] Can register with valid data
- [ ] Cannot register with duplicate email
- [ ] Can login with correct credentials
- [ ] Cannot login with wrong password
- [ ] Can logout
- [ ] Can fetch authenticated user
- [ ] Cannot access protected routes without token

#### SuggestionTest
- [ ] Can list suggestions (paginated)
- [ ] Can filter by status
- [ ] Can filter by category
- [ ] Can search by keyword
- [ ] Can sort by latest/oldest/popular
- [ ] Can create suggestion as authenticated user
- [ ] Cannot create suggestion as guest
- [ ] Can view single suggestion
- [ ] Can update own suggestion
- [ ] Cannot update another's suggestion (as employee)
- [ ] Moderator can update any suggestion
- [ ] Can delete own pending suggestion
- [ ] Cannot delete own non-pending suggestion
- [ ] Anonymous author is masked
- [ ] Suggestion with invalid data returns validation errors

#### CommentTest
- [ ] Can list comments on suggestion
- [ ] Can create comment as authenticated user
- [ ] Cannot create comment as guest
- [ ] Can delete own comment
- [ ] Moderator can delete any comment
- [ ] Cannot delete another's comment (as employee)

#### VoteTest
- [ ] Can upvote suggestion
- [ ] Can downvote suggestion
- [ ] Toggle removes vote if same direction
- [ ] Can change vote direction
- [ ] Can remove vote
- [ ] Cannot vote on own suggestion? (decide policy)
- [ ] Vote counts update correctly
- [ ] Cannot vote as guest

#### ModerateTest
- [ ] Moderator can change status
- [ ] Invalid status transition returns 422
- [ ] Employee cannot change status
- [ ] Admin can change any status
- [ ] Pending suggestions list returns only pending

#### AdminTest
- [ ] Dashboard returns stats
- [ ] Can list all users
- [ ] Can update user role
- [ ] Cannot delete own admin account
- [ ] Employee cannot access admin routes
- [ ] Can force-delete suggestion

### 13.2 Feature Tests with SQLite

Use in-memory SQLite for fast tests:
```php
// phpunit.xml
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>
```

### 13.3 Factories for Test Data

Each test file should use factories to create test data:
```php
$user = User::factory()->create();
$suggestion = Suggestion::factory()->for($user)->create();
$comment = Comment::factory()->for($suggestion)->for($user)->create();
```

### 13.4 Frontend — Vitest Tests

```bash
npm install -D vitest @vue/test-utils happy-dom
```

#### Component Tests
- [ ] StatusBadge renders correct color for each status
- [ ] SuggestionCard displays title, author, counts
- [ ] VoteButton emits event on click
- [ ] Login form validates required fields
- [ ] Pagination navigates correctly

#### Store Tests
- [ ] Auth store sets token on login
- [ ] Auth store clears token on logout
- [ ] Suggestion store applies filters correctly

### 13.5 Testing Commands

```bash
# Backend
composer test                  # Run all tests
php artisan test --filter=SuggestionTest

# Frontend
npm run test:unit              # Vitest
npm run test:e2e               # Playwright/Cypress if configured
```

## Deliverables
- [ ] All backend tests passing
- [ ] Coverage for auth, CRUD, votes, comments, moderation, admin
- [ ] Factory usage for test data
- [ ] In-memory SQLite test DB
- [ ] Frontend component tests
- [ ] Frontend store tests
- [ ] GitHub Actions CI passing
