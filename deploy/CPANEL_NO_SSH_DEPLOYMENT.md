# cPanel Deployment Without SSH

This shared-hosting workflow assumes cPanel has no Terminal and no SSH.

## Local Build

Run these commands on a machine that has PHP 8.2+ and Composer:

```bash
composer install --no-dev --optimize-autoloader
copy .env.example .env
php artisan key:generate
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

If you already have a valid `.env`, do not overwrite it. Just update production values before upload.

## Upload Layout

Upload the Laravel app outside `public_html`:

```text
/home/username/daatio
```

Copy the contents of `deploy/cpanel-public_html` into:

```text
/home/username/public_html
```

Also copy any built assets from the Laravel `public` folder into `public_html`, such as `build/`, images, CSS, or JS.

The provided `index.php` assumes the app folder is named `daatio`:

```php
require __DIR__.'/../daatio/vendor/autoload.php';
$app = require_once __DIR__.'/../daatio/bootstrap/app.php';
```

If your app folder has a different name, update those paths.

## Environment File

Create this file on the server:

```text
/home/username/daatio/.env
```

Use `deploy/cpanel.env.example` as the template. Update:

- `APP_KEY`
- `APP_URL`
- `DB_DATABASE`
- `DB_USERNAME`
- `DB_PASSWORD`

## Database

In cPanel:

1. Create the database and database user.
2. Add the user to the database with all privileges.
3. Open phpMyAdmin.
4. Import `database.sql`.
5. Import `deploy/sql/001_sanctum_personal_access_tokens.sql`.

The Sanctum table is required for API bearer tokens from `/api/auth/login`.

## Writable Folders

Make sure these folders are writable by PHP:

```text
storage
bootstrap/cache
```

On many shared hosts, folder permission `755` is enough. Some hosts require `775`.

## API Check

After upload, visit:

```text
https://yourdomain.com/api/status
```

Expected response:

```json
{"name":"Daatio","status":"ok"}
```
