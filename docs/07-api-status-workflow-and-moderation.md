# Phase 7: API — Status Workflow & Moderation

## Goal
Implement the suggestion lifecycle with status transitions, moderation review, and admin tools.

## Steps

### 7.1 Status Definitions

```php
enum SuggestionStatus: string
{
    case Pending = 'pending';
    case UnderReview = 'under_review';
    case Approved = 'approved';
    case Declined = 'declined';
    case Implemented = 'implemented';
}
```

As string constants since SQLite doesn't support enums:

```php
class Suggestion extends Model
{
    const STATUS_PENDING = 'pending';
    const STATUS_UNDER_REVIEW = 'under_review';
    const STATUS_APPROVED = 'approved';
    const STATUS_DECLINED = 'declined';
    const STATUS_IMPLEMENTED = 'implemented';
}
```

### 7.2 Status Transition Rules

| From → To | Permitted? | By Whom |
|-----------|-----------|---------|
| pending → under_review | ✅ | Moderator, Admin |
| pending → declined | ✅ | Moderator, Admin |
| under_review → approved | ✅ | Moderator, Admin |
| under_review → declined | ✅ | Moderator, Admin |
| approved → implemented | ✅ | Admin |
| approved → under_review | ✅ | Moderator, Admin |
| (any) → (same) | ❌ | No-op |

### 7.3 Status Transition Method on Suggestion

```php
public function transitionTo(string $newStatus, User $reviewer): bool
{
    $allowed = match ($this->status) {
        self::STATUS_PENDING => [self::STATUS_UNDER_REVIEW, self::STATUS_DECLINED],
        self::STATUS_UNDER_REVIEW => [self::STATUS_APPROVED, self::STATUS_DECLINED],
        self::STATUS_APPROVED => [self::STATUS_IMPLEMENTED, self::STATUS_UNDER_REVIEW],
        default => [],
    };

    if (!in_array($newStatus, $allowed)) {
        return false;
    }

    $this->update([
        'status' => $newStatus,
        'reviewed_by' => $reviewer->id,
        'reviewed_at' => now(),
    ]);

    return true;
}
```

### 7.4 Moderator Endpoints

| Method | URI | Auth | Description |
|--------|-----|------|-------------|
| PUT | `/api/suggestions/{suggestion}/status` | Moderator+ | Change suggestion status |
| GET | `/api/moderator/suggestions/pending` | Moderator+ | List pending suggestions |
| GET | `/api/moderator/suggestions/reported` | Moderator+ | List flagged content (future) |

### 7.5 Status Update Request

```php
// StoreSuggestionStatusRequest
public function rules(): array
{
    return [
        'status' => ['required', 'string', 'in:under_review,approved,declined,implemented'],
        'note' => ['nullable', 'string', 'max:500'], // optional moderation note
    ];
}
```

### 7.6 ModerateController

```php
class ModerateController extends Controller
{
    public function updateStatus(StoreSuggestionStatusRequest $request, Suggestion $suggestion)
    {
        $this->authorize('moderate', $suggestion);

        $transitioned = $suggestion->transitionTo($request->status, auth()->user());

        if (!$transitioned) {
            return response()->json([
                'message' => 'Invalid status transition from ' . $suggestion->status,
            ], 422);
        }

        return response()->json([
            'message' => 'Suggestion status updated',
            'suggestion' => new SuggestionResource($suggestion->fresh()),
        ]);
    }

    public function pending()
    {
        $this->authorize('moderate', Suggestion::class);

        $suggestions = Suggestion::where('status', Suggestion::STATUS_PENDING)
            ->with(['user', 'category'])
            ->withCount('comments')
            ->latest()
            ->paginate(15);

        return SuggestionResource::collection($suggestions);
    }
}
```

### 7.7 Moderation Log (Optional)

Consider a `moderation_logs` table to track all status changes:
```php
Schema::create('moderation_logs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('suggestion_id')->constrained()->cascadeOnDelete();
    $table->foreignId('user_id')->constrained(); // moderator who acted
    $table->string('from_status');
    $table->string('to_status');
    $table->text('note')->nullable();
    $table->timestamps();
});
```

### 7.8 Suggestion Deletion

- Soft-delete suggestions instead of hard delete
- Owner can delete their own suggestion (only if pending)
- Moderator/admin can delete any suggestion at any time

```bash
php artisan make:migration add_soft_deletes_to_suggestions_table
```

Add `SoftDeletes` trait to Suggestion model and filter only non-deleted in queries.

## Deliverables
- [ ] Status transition logic with validation
- [ ] Moderator endpoints for status changes
- [ ] Pending suggestions queue endpoint
- [ ] Soft deletes implemented
- [ ] Optional: moderation_logs table
- [ ] Authorization: only moderator+ can change status
- [ ] Error handling for invalid transitions
