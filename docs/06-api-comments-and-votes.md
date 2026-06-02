# Phase 6: API — Comments & Votes

## Goal
Implement social features: commenting on suggestions and voting (upvote/downvote).

## Steps

### 6.1 CommentController
```bash
php artisan make:controller Api/CommentController --resource --model=Comment
php artisan make:request StoreCommentRequest
php artisan make:resource CommentResource
```

### 6.2 Comment Endpoints

| Method | URI | Auth | Description |
|--------|-----|------|-------------|
| GET | `/api/suggestions/{suggestion}/comments` | Optional | List comments on a suggestion |
| POST | `/api/suggestions/{suggestion}/comments` | Required | Add comment |
| DELETE | `/api/comments/{comment}` | Required | Delete own comment (or moderator) |

### 6.3 StoreCommentRequest
```php
public function rules(): array
{
    return [
        'body' => ['required', 'string', 'min:1', 'max:2000'],
    ];
}
```

### 6.4 CommentResource
```php
class CommentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'body' => $this->body,
            'user' => new UserResource($this->whenLoaded('user')),
            'suggestion_id' => $this->suggestion_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'can_delete' => $request->user()?->can('delete', $this->resource),
        ];
    }
}
```

### 6.5 Comment Authorization

- Owner can delete their own comment
- Moderator/admin can delete any comment
- Use a CommentPolicy:
```bash
php artisan make:policy CommentPolicy --model=Comment
```

### 6.6 VoteController
```bash
php artisan make:controller Api/VoteController
```

### 6.7 Vote Endpoints

| Method | URI | Auth | Description |
|--------|-----|------|-------------|
| POST | `/api/suggestions/{suggestion}/vote` | Required | Upvote or downvote |
| DELETE | `/api/suggestions/{suggestion}/vote` | Required | Remove vote |

### 6.8 Vote Logic

```php
public function store(Request $request, Suggestion $suggestion)
{
    $request->validate(['vote' => ['required', 'in:up,down']]);

    $existing = $suggestion->votes()->where('user_id', auth()->id())->first();

    if ($existing) {
        // Toggle: if same vote, remove it; if different, switch
        if ($existing->vote === $request->vote) {
            $existing->delete();
            $message = 'Vote removed';
        } else {
            $existing->update(['vote' => $request->vote]);
            $message = 'Vote changed';
        }
    } else {
        $suggestion->votes()->create([
            'user_id' => auth()->id(),
            'vote' => $request->vote,
        ]);
        $message = 'Vote cast';
    }

    // Return updated counts
    return response()->json([
        'message' => $message,
        'upvotes_count' => $suggestion->votes()->where('vote', 'up')->count(),
        'downvotes_count' => $suggestion->votes()->where('vote', 'down')->count(),
        'user_vote' => $suggestion->votes()->where('user_id', auth()->id())->first()?->vote,
    ]);
}
```

### 6.9 Vote Removal
```php
public function destroy(Suggestion $suggestion)
{
    $suggestion->votes()
        ->where('user_id', auth()->id())
        ->delete();

    return response()->json(['message' => 'Vote removed']);
}
```

### 6.10 User Vote State on Suggestions

When returning a suggestion list for an authenticated user, include whether they voted:

```php
// On SuggestionResource when user is authenticated
'user_vote' => $this->when(
    $request->user(),
    fn() => $this->votes
        ->where('user_id', $request->user()->id)
        ->first()?->vote
),
```

## Deliverables
- [ ] Comments: create, list, delete with authorization
- [ ] Votes: upvote, downvote, toggle, remove
- [ ] Vote counts returned with suggestion
- [ ] User's current vote state included in suggestion response
- [ ] Policies for comment authorization
- [ ] Preventing self-voting? (optional: allow or block)
