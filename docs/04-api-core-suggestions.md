# Phase 4: API — Core Suggestions CRUD

## Goal
Build the full REST API for suggestions — create, read, update, delete, with search, filter, pagination.

## Steps

### 4.1 SuggestionController

```bash
php artisan make:controller Api/SuggestionController --resource --model=Suggestion
php artisan make:request StoreSuggestionRequest
php artisan make:request UpdateSuggestionRequest
php artisan make:resource SuggestionResource
```

### 4.2 Endpoints

| Method | URI | Auth | Controller Action |
|--------|-----|------|-------------------|
| GET | `/api/suggestions` | Optional | index |
| POST | `/api/suggestions` | Required | store |
| GET | `/api/suggestions/{suggestion}` | Optional | show |
| PUT | `/api/suggestions/{suggestion}` | Required | update |
| DELETE | `/api/suggestions/{suggestion}` | Required | destroy |

### 4.3 StoreSuggestionRequest
```php
public function authorize(): bool
{
    return auth()->check(); // any authenticated user
}

public function rules(): array
{
    return [
        'title' => ['required', 'string', 'min:5', 'max:255'],
        'description' => ['required', 'string', 'min:20', 'max:10000'],
        'category_id' => ['nullable', 'exists:categories,id'],
        'anonymous' => ['boolean'],
    ];
}
```

### 4.4 Index (List with Filters)

Query parameters:
- `?status=pending` — filter by status
- `?category_id=1` — filter by category
- `?search=keyword` — search title & description
- `?sort=latest|oldest|popular` — sort order
- `?per_page=15` — pagination (default: 15, max: 50)

Implementation:
```php
public function index(Request $request)
{
    $query = Suggestion::with(['user', 'category', 'votes'])
        ->withCount(['comments', 'votes as upvotes_count' => fn($q) => $q->where('vote', 'up')])
        ->withCount(['votes as downvotes_count' => fn($q) => $q->where('vote', 'down')]);

    // Apply filters
    if ($request->status) {
        $query->where('status', $request->status);
    }
    if ($request->category_id) {
        $query->where('category_id', $request->category_id);
    }
    if ($request->search) {
        $query->where(function ($q) use ($request) {
            $q->where('title', 'like', "%{$request->search}%")
              ->orWhere('description', 'like', "%{$request->search}%");
        });
    }

    // Sort
    match ($request->sort) {
        'oldest' => $query->oldest(),
        'popular' => $query->withCount('votes')->orderBy('votes_count', 'desc'),
        default => $query->latest(), // 'latest'
    };

    return SuggestionResource::collection($query->paginate($request->per_page ?? 15));
}
```

### 4.5 Show (Single Suggestion)

Eager-load relationships:
```php
$suggestion->load(['user', 'category', 'comments.user', 'votes']);
$suggestion->loadCount(['comments', 'votes as upvotes_count' => fn($q) => $q->where('vote', 'up')]);
$suggestion->loadCount(['votes as downvotes_count' => fn($q) => $q->where('vote', 'down')]);
```

### 4.6 Update Authorization

Only owner, moderator, or admin can update:
```php
public function authorize(): bool
{
    $suggestion = $this->route('suggestion');
    return auth()->user()->isAdmin()
        || auth()->user()->isModerator()
        || $suggestion->user_id === auth()->id();
}
```

### 4.7 Anonymous Handling

- If `anonymous` is true, replace `user_id` with a generic "Anonymous" display in the resource
- Store the real `user_id` for DB tracking, only mask in API response

### 4.8 SuggestionResource

```php
class SuggestionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'anonymous' => $this->anonymous,
            'upvotes_count' => $this->upvotes_count ?? 0,
            'downvotes_count' => $this->downvotes_count ?? 0,
            'comments_count' => $this->comments_count ?? 0,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        if ($this->anonymous) {
            $data['author'] = 'Anonymous';
        } else {
            $data['author'] = new UserResource($this->whenLoaded('user'));
        }

        return $data;
    }
}
```

## Deliverables
- [ ] All CRUD endpoints working
- [ ] Pagination with configurable per_page
- [ ] Filtering by status, category, search keyword
- [ ] Sorting (latest, oldest, popular)
- [ ] Authorization enforced (owner/moderator for updates)
- [ ] Anonymous author masking
- [ ] Resource classes returning clean JSON
