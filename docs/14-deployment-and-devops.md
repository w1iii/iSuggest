# Phase 14: Deployment & DevOps

## Goal
Prepare the application for production deployment with Docker, CI/CD, and monitoring.

## Steps

### 14.1 Docker Setup

**Dockerfile (backend):**
```dockerfile
FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev zip unzip \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
COPY backend/ .

RUN composer install --optimize-autoloader --no-dev
RUN php artisan optimize

RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]
```

**docker-compose.yml:**
```yaml
version: '3.8'
services:
  app:
    build:
      context: .
      dockerfile: Dockerfile
    ports:
      - "9000:9000"
    volumes:
      - ./backend:/var/www
    depends_on:
      - db

  db:
    image: postgres:16-alpine
    environment:
      POSTGRES_DB: suggestion_box
      POSTGRES_USER: app
      POSTGRES_PASSWORD: ${DB_PASSWORD}
    volumes:
      - pgdata:/var/lib/postgresql/data
    ports:
      - "5432:5432"

  nginx:
    image: nginx:alpine
    ports:
      - "80:80"
    volumes:
      - ./backend:/var/www
      - ./docker/nginx.conf:/etc/nginx/conf.d/default.conf
    depends_on:
      - app

  frontend:
    build:
      context: ./frontend
      dockerfile: Dockerfile
    ports:
      - "5173:80"

volumes:
  pgdata:
```

### 14.2 Nginx Configuration

```nginx
server {
    listen 80;
    server_name api.suggestionbox.local;
    root /var/www/public;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass app:9000;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### 14.3 Environment Variables (Production)

```
APP_ENV=production
APP_DEBUG=false
APP_URL=https://suggestionbox.example.com

DB_CONNECTION=pgsql
DB_HOST=db
DB_PORT=5432
DB_DATABASE=suggestion_box
DB_USERNAME=app
DB_PASSWORD=${DB_PASSWORD}

MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=${SENDGRID_API_KEY}

QUEUE_CONNECTION=database
SESSION_DRIVER=redis
CACHE_DRIVER=redis
REDIS_HOST=redis
```

### 14.4 CI/CD Pipeline (GitHub Actions)

```yaml
# .github/workflows/ci.yml
name: CI

on: [push, pull_request]

jobs:
  backend-tests:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: '8.3'
      - run: composer install
        working-directory: backend
      - run: cp .env.example .env
        working-directory: backend
      - run: php artisan key:generate
        working-directory: backend
      - run: php artisan test
        working-directory: backend

  frontend-tests:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: actions/setup-node@v4
        with:
          node-version: '20'
      - run: npm ci
        working-directory: frontend
      - run: npm run test:unit
        working-directory: frontend
      - run: npm run build
        working-directory: frontend

  deploy:
    needs: [backend-tests, frontend-tests]
    if: github.ref == 'refs/heads/main'
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - name: Deploy to production
        run: |
          # Example: deploy via SSH
          ssh deploy@example.com "cd /var/www/suggestion-box && git pull && docker-compose up -d --build"
```

### 14.5 Post-Deployment Checklist

- [ ] Database migration ran successfully
- [ ] Queue worker is running (`php artisan queue:work`)
- [ ] Schedule is running (`* * * * * php artisan schedule:run`)
- [ ] Storage directory is writable
- [ ] SSL certificate valid (Let's Encrypt)
- [ ] Frontend assets are built and served from CDN or Nginx
- [ ] CORS configured for production domain
- [ ] Rate limiting enabled on API routes
- [ ] Error monitoring (Sentry/Flares) configured
- [ ] Backup strategy in place (DB + storage)
- [ ] Health check endpoint responds `200`

### 14.6 Performance Optimizations

- [ ] Route caching: `php artisan route:cache`
- [ ] Config caching: `php artisan config:cache`
- [ ] View caching: `php artisan view:cache`
- [ ] Event caching: `php artisan event:cache`
- [ ] OPcache enabled in PHP
- [ ] Database indexing on: `status`, `category_id`, `user_id` in suggestions
- [ ] Nginx gzip compression enabled
- [ ] Frontend lazy-load route components
- [ ] API response pagination capped at 50 per page

## Deliverables
- [ ] Docker setup (app, nginx, db, queue)
- [ ] GitHub Actions CI passing
- [ ] Deployment script or action
- [ ] Production .env template
- [ ] Post-deployment checklist
- [ ] Performance optimizations applied
- [ ] Monitoring configured
