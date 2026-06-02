# Phase 5: API — Categories

## Goal
Build CRUD for categories (admin-only for write operations), and integrate with suggestions.

## Steps

### 5.1 CategoryController
```bash
php artisan make:controller Api/CategoryController --resource --model=Category
php artisan make:request StoreCategoryRequest
php artisan make:resource CategoryResource
```

### 5.2 Endpoints

| Method | URI | Auth | Role | Description |
|--------|-----|------|------|-------------|
| GET | `/api/categories` | Optional | — | List all categories |
| GET | `/api/categories/{category}` | Optional | — | Show single category |
| POST | `/api/categories` | Required | Admin | Create category |
| PUT | `/api/categories/{category}` | Required | Admin | Update category |
| DELETE | `/api/categories/{category}` | Required | Admin | Delete category |

### 5.3 Category With Counts

Index should return each category with suggestion count:
```php
Category::withCount('suggestions')->get();
```

Optionally include recent suggestions under a category:
```php
Category::with(['suggestions' => fn($q) => $q->latest()->limit(5)])->get();
```

### 5.4 Default Categories to Seed

| Name | Slug | Icon | Color |
|------|------|------|-------|
| 🐛 Bug Report | bug-report | bug-report | #ef4444 |
| ✨ Feature Request | feature-request | feature-request | #8b5cf6 |
| 🔧 Improvement | improvement | improvement | #3b82f6 |
| 🏢 Workplace | workplace | workplace | #10b981 |
| 📋 Process | process | process | #f59e0b |
| 💡 Other | other | other | #6b7280 |

### 5.5 CategoryResource
```php
class CategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'icon' => $this->icon,
            'color' => $this->color,
            'suggestions_count' => $this->whenAggregated('suggestions', 'id', 'count'),
            'created_at' => $this->created_at,
        ];
    }
}
```

### 5.6 Validation (StoreCategoryRequest)
```php
public function rules(): array
{
    return [
        'name' => ['required', 'string', 'max:100', Rule::unique('categories')],
        'slug' => ['required', 'string', 'max:100', Rule::unique('categories'), 'regex:/^[a-z0-9-]+$/'],
        'description' => ['nullable', 'string', 'max:500'],
        'icon' => ['nullable', 'string', 'max:50'],
        'color' => ['nullable', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
    ];
}
```

## Deliverables
- [ ] Category CRUD (admin-only for write)
- [ ] Categories returned with suggestion counts
- [ ] Slug auto-generation on create
- [ ] Validation rules for color hex, unique slug, etc.
- [ ] Integration: suggestions filterable by category
