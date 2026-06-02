# Phase 9: Frontend Foundation

## Goal
Set up the Vue 3 frontend architecture: routing, layout, auth UI, API client, Pinia stores.

## Steps

### 9.1 Project Architecture

```
frontend/src/
├── api/                  # Axios/Api client
│   ├── client.js         # Axios instance with interceptors
│   ├── auth.js           # Auth API calls
│   ├── suggestions.js    # Suggestions API calls
│   ├── categories.js     # Categories API calls
│   ├── comments.js       # Comments API calls
│   └── admin.js          # Admin API calls
├── components/           # Reusable components
│   ├── layout/
│   │   ├── AppLayout.vue
│   │   ├── Navbar.vue
│   │   └── Sidebar.vue
│   ├── ui/
│   │   ├── Button.vue
│   │   ├── Modal.vue
│   │   ├── Pagination.vue
│   │   ├── Badge.vue
│   │   └── Alert.vue
│   └── suggestions/
│       ├── SuggestionCard.vue
│       ├── SuggestionList.vue
│       └── VoteButton.vue
├── composables/          # Composition API hooks
│   ├── useAuth.js
│   └── usePagination.js
├── router/
│   └── index.js          # Vue Router config
├── stores/
│   ├── auth.js           # Auth Pinia store
│   └── suggestions.js    # Suggestions Pinia store
├── views/
│   ├── auth/
│   │   ├── LoginView.vue
│   │   └── RegisterView.vue
│   ├── suggestions/
│   │   ├── IndexView.vue
│   │   ├── ShowView.vue
│   │   └── CreateView.vue
│   └── admin/
│       ├── DashboardView.vue
│       └── UsersView.vue
├── App.vue
└── main.js
```

### 9.2 API Client Setup

```js
// api/client.js
import axios from 'axios';
import { useAuthStore } from '@/stores/auth';
import router from '@/router';

const client = axios.create({
    baseURL: import.meta.env.VITE_API_URL || '/api',
    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
});

// Request interceptor: attach token
client.interceptors.request.use((config) => {
    const token = localStorage.getItem('token');
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
});

// Response interceptor: handle 401
client.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response?.status === 401) {
            localStorage.removeItem('token');
            router.push('/login');
        }
        return Promise.reject(error);
    }
);

export default client;
```

### 9.3 Routes
```js
const routes = [
    { path: '/', name: 'home', component: HomeView },
    { path: '/login', name: 'login', component: LoginView, meta: { guest: true } },
    { path: '/register', name: 'register', component: RegisterView, meta: { guest: true } },
    { path: '/suggestions', name: 'suggestions.index', component: SuggestionIndexView },
    { path: '/suggestions/create', name: 'suggestions.create', component: SuggestionCreateView, meta: { auth: true } },
    { path: '/suggestions/:id', name: 'suggestions.show', component: SuggestionShowView },
    { path: '/admin', name: 'admin.dashboard', component: AdminDashboardView, meta: { auth: true, role: 'admin' } },
    { path: '/admin/users', name: 'admin.users', component: AdminUsersView, meta: { auth: true, role: 'admin' } },
];
```

### 9.4 Auth Store (Pinia)
```js
export const useAuthStore = defineStore('auth', () => {
    const user = ref(null);
    const token = ref(localStorage.getItem('token'));
    const isAuthenticated = computed(() => !!token.value);

    async function login(email, password) { /* ... */ }
    async function register(name, email, password) { /* ... */ }
    async function logout() { /* ... */ }
    async function fetchUser() { /* ... */ }

    return { user, token, isAuthenticated, login, register, logout, fetchUser };
});
```

### 9.5 Auth Views

**LoginView**: email + password form → calls auth store login → redirect to /suggestions
**RegisterView**: name + email + password + password_confirmation → calls auth store register → redirect

### 9.6 Navigation Guard
```js
router.beforeEach((to, from, next) => {
    const auth = useAuthStore();
    if (to.meta.auth && !auth.isAuthenticated) {
        next({ name: 'login', query: { redirect: to.fullPath } });
    } else if (to.meta.guest && auth.isAuthenticated) {
        next({ name: 'home' });
    } else {
        next();
    }
});
```

### 9.7 Environment Variables

```
VITE_API_URL=http://localhost:8000/api
```

## Deliverables
- [ ] Axios client configured with token interceptors
- [ ] Vue Router with auth guards
- [ ] Auth store (Pinia) with login/register/logout
- [ ] Login and Register views working
- [ ] AppLayout with Navbar (conditional on auth state)
- [ ] Route protection (redirect to login if unauthenticated)
- [ ] Role-based guard for admin routes
