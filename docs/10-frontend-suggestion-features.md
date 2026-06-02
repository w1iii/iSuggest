# Phase 10: Frontend — Suggestion Features

## Goal
Build the core suggestion browsing, creation, and detail views with filtering, search, and pagination.

## Steps

### 10.1 Suggestions Store

```js
// stores/suggestions.js
export const useSuggestionStore = defineStore('suggestions', () => {
    const suggestions = ref([]);
    const currentSuggestion = ref(null);
    const pagination = ref({});
    const filters = ref({ status: null, category_id: null, search: '', sort: 'latest' });
    const loading = ref(false);

    async function fetchSuggestions() { /* GET /suggestions with filters */ }
    async function fetchSuggestion(id) { /* GET /suggestions/:id */ }
    async function createSuggestion(data) { /* POST /suggestions */ }
    async function updateSuggestion(id, data) { /* PUT /suggestions/:id */ }
    async function deleteSuggestion(id) { /* DELETE /suggestions/:id */ }

    return { suggestions, currentSuggestion, pagination, filters, loading,
             fetchSuggestions, fetchSuggestion, createSuggestion, updateSuggestion, deleteSuggestion };
});
```

### 10.2 Suggestion Index View

Features:
- [ ] **Filters bar**: status dropdown, category dropdown, search input, sort selector
- [ ] **Suggestion cards**: each card shows title, author, status badge, vote count, comment count, category, date
- [ ] **Pagination**: page navigation at bottom
- [ ] **Loading state**: skeleton / spinner
- [ ] **Empty state**: friendly message when no results
- [ ] **Responsive grid**: cards in a 1/2/3-column grid based on viewport

### 10.3 Suggestion Card Component

```vue
<SuggestionCard :suggestion="suggestion" />
<!-- Displays:
- Category badge (colored)
- Status badge (colored: green=approved, yellow=pending, red=declined)
- Title (link to detail)
- Author (or "Anonymous")
- Upvote count + Downvote count
- Comment count
- Created date (relative: "2 days ago")
-->
```

### 10.4 Suggestion Create View

- [ ] Form with: title, description (textarea), category (dropdown), anonymous (checkbox)
- [ ] Validation: title min 5 chars, description min 20 chars
- [ ] Submit → redirect to the new suggestion detail page
- [ ] Auth guard: must be logged in

### 10.5 Suggestion Show View (Detail)

- [ ] Full suggestion detail: title, description, author, status, category, dates
- [ ] Vote buttons (up/down) with counts — real-time update
- [ ] Comment section (Phase 11)
- [ ] Edit/Delete buttons if owner or moderator
- [ ] Status change dropdown if moderator+

### 10.6 Status Badge Component

```vue
<StatusBadge :status="suggestion.status" />
<!-- pending → yellow "Pending"
     under_review → blue "Under Review"
     approved → green "Approved"
     declined → red "Declined"
     implemented → purple "Implemented" -->
```

### 10.7 UI Component Library

Build or use minimal custom components:
- `BaseButton.vue` — primary, secondary, danger, ghost variants
- `BaseInput.vue` — with label, validation error display
- `BaseTextarea.vue` — with label, validation error
- `BaseSelect.vue` — with label, options
- `BaseBadge.vue` — colored label
- `BasePagination.vue` — page numbers, prev/next
- `BaseModal.vue` — overlay modal with close
- `BaseAlert.vue` — success, error, info messages

## Deliverables
- [ ] Suggestion index with filtering, search, pagination
- [ ] Suggestion creation form with validation
- [ ] Suggestion detail page
- [ ] Status badges with colors
- [ ] Vote buttons on detail page
- [ ] Loading, empty, error states
- [ ] Responsive layout
