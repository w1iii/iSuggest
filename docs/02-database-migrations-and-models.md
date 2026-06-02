# Phase 2: Database Migrations & Models

## Goal
Create all database migrations, Eloquent models, relationships, factories, and seeders.

## Steps

### 2.1 Create Migrations

#### Users Table (enhance existing)
- Add `role` enum column: `employee`, `moderator`, `admin`
- Add `department` string (nullable)
- Add `avatar` string (nullable)

#### Suggestions Table (new)
```php
Schema::create('suggestions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
    $table->string('title');
    $table->text('description');
    $table->string('status')->default('pending');
        // enum: pending, under_review, approved, declined, implemented
    $table->boolean('anonymous')->default(false);
    $table->timestamps();
    $table->timestamp('reviewed_at')->nullable();
    $table->foreignId('reviewed_by')->nullable()->constrained('users');
});
```

#### Categories Table (new)
```php
Schema::create('categories', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('slug')->unique();
    $table->text('description')->nullable();
    $table->string('icon')->nullable();
    $table->string('color')->nullable();
    $table->timestamps();
});
```

#### Comments Table (new)
```php
Schema::create('comments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('suggestion_id')->constrained()->cascadeOnDelete();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->text('body');
    $table->timestamps();
});
```

#### Votes Table (new)
```php
Schema::create('votes', function (Blueprint $table) {
    $table->id();
    $table->foreignId('suggestion_id')->constrained()->cascadeOnDelete();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->string('vote'); // 'up' or 'down'
    $table->timestamps();
    $table->unique(['suggestion_id', 'user_id']);
});
```

### 2.2 Create Models

| Model | Fillable | Casts | Relationships |
|-------|----------|-------|---------------|
| User | name, email, password, role, department, avatar | role: string | hasMany(Suggestion), hasMany(Comment), hasMany(Vote) |
| Suggestion | user_id, category_id, title, description, status, anonymous, reviewed_by | status: string, anonymous: boolean | belongsTo(User), belongsTo(Category), hasMany(Comment), hasMany(Vote) |
| Category | name, slug, description, icon, color | — | hasMany(Suggestion) |
| Comment | suggestion_id, user_id, body | — | belongsTo(Suggestion), belongsTo(User) |
| Vote | suggestion_id, user_id, vote | vote: string | belongsTo(Suggestion), belongsTo(User) |

### 2.3 Model Boolean Methods

Add helper methods to Suggestion:
- `isPending()` / `isUnderReview()` / `isApproved()` / `isDeclined()` / `isImplemented()`
- `canBeReviewedBy(User $user)` — moderator or admin
- `ownedBy(User $user)` — check ownership

Add to User:
- `isAdmin()` / `isModerator()` / `isEmployee()`

### 2.4 Factories & Seeders

- [ ] **CategoryFactory** + **CategorySeeder**: Create 6–8 default categories (Bug Report, Feature Request, Improvement, Workplace, Process, Other)
- [ ] **UserFactory**: Generate employees, one moderator, one admin
- [ ] **SuggestionFactory**: Generate sample suggestions across categories and statuses
- [ ] **CommentFactory**: Generate comments on suggestions
- [ ] **VoteFactory**: Generate votes on suggestions
- [ ] **DatabaseSeeder**: Orchestrate all seeders in dependency order

### 2.5 SQLite Compatibility
- Use `string('status')` instead of `enum()` for SQLite compatibility
- Use `nullableConstraints()` where needed

## Commands
```bash
# Create migrations
php artisan make:migration add_role_and_department_to_users_table
php artisan make:migration create_categories_table
php artisan make:migration create_suggestions_table
php artisan make:migration create_comments_table
php artisan make:migration create_votes_table

# Create models
php artisan make:model Suggestion -mf
php artisan make:model Category -mf
php artisan make:model Comment -mf
php artisan make:model Vote -mf

# Create factories & seeders
php artisan make:factory CategoryFactory --model=Category
php artisan make:factory SuggestionFactory --model=Suggestion
php artisan make:factory CommentFactory --model=Comment
php artisan make:factory VoteFactory --model=Vote
php artisan make:seeder CategorySeeder
php artisan make:seeder SuggestionSeeder

# Run
php artisan migrate --seed
```

## Deliverables
- [ ] All migrations created and runnable
- [ ] All models with relationships defined
- [ ] Factories for test/seeding data
- [ ] DatabaseSeeder orchestrates all seeders
- [ ] `php artisan migrate:fresh --seed` succeeds
