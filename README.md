# Daatio

Daatio is a Laravel application scaffolded with a modular architecture.

## Modules

The project is prepared for `nwidart/laravel-modules` and starts with these domain modules:

- `Users`
- `Stores`
- `Clients`
- `Products`
- `Transactions`

## First Run

Install PHP 8.2+ and Composer, then run:

```bash
composer install
copy .env.example .env
php artisan key:generate
php artisan vendor:publish --provider="Nwidart\Modules\LaravelModulesServiceProvider"
php artisan serve
```

If you want frontend assets:

```bash
npm install
npm run dev
```

The existing `database.sql` file was preserved.

## Database Workflow

Laravel's native Prisma-like workflow is Eloquent models plus migrations. For this project, two helper packages are prepared:

- `kitloong/laravel-migrations-generator`: generate Laravel migrations from the existing `daatio` database.
- `reliese/laravel`: generate Eloquent models from the existing database schema.

After `composer install`, connect `.env` to the local `daatio` database and run:

```bash
php artisan migrate:generate
php artisan code:models
```

The current hand-written models live inside their domain modules under `Modules/*/app/Models`.

## Authentication API

Install dependencies and run migrations first:

```bash
composer install
php artisan key:generate
php artisan migrate
```

Register:

```http
POST /api/auth/register
Content-Type: application/json

{
  "tenant_id": 1,
  "user_name": "Ada",
  "user_lastName": "Lovelace",
  "user_email": "ada@example.com",
  "user_access": "owner",
  "user_password": "password123",
  "user_password_confirmation": "password123",
  "user_phone_number": "+10000000000"
}
```

Login:

```http
POST /api/auth/login
Content-Type: application/json

{
  "user_email": "ada@example.com",
  "user_password": "password123"
}
```

Protected requests:

```http
GET /api/auth/me
Authorization: Bearer YOUR_TOKEN
```

```http
POST /api/auth/logout
Authorization: Bearer YOUR_TOKEN
```

## Shared cPanel Without SSH

Deployment files are included in `deploy/`:

- `deploy/CPANEL_NO_SSH_DEPLOYMENT.md`
- `deploy/cpanel.env.example`
- `deploy/cpanel-public_html/index.php`
- `deploy/cpanel-public_html/.htaccess`
- `deploy/sql/001_sanctum_personal_access_tokens.sql`

Because shared cPanel has no shell, install Composer dependencies locally and upload the project with the `vendor` folder included.
