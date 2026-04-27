# Security Documentation

## Authentication

- Laravel session-based authentication (no JWT/API tokens for web UI)
- Login endpoint: `GET/POST /login`
- Logout endpoint: `POST /logout` — requires CSRF token; GET logout is not supported
- Failed login redirects back to the login form with validation errors; credentials are not exposed in error messages
- Passwords are hashed with bcrypt via Laravel's `Hash` facade

**Known gap:** No rate limiting on login attempts in the MVP. See [Recommendations](#recommendations).

---

## Authorization

### PlatformMiddleware
Guards all routes under `/platform/*`. Allows access only to users where `tenant_id IS NULL` (super-admins). Any authenticated user with a `tenant_id` is rejected with a 403.

### TenantMiddleware
Guards all tenant application routes. Requires:
1. The user is authenticated
2. The user has a non-null `tenant_id`
3. The user's tenant exists and is **active** (not suspended)

A suspended tenant's users are redirected to a suspension notice page until re-activated via the platform admin panel.

### Role & Permission Checks
Fine-grained permissions are managed with `spatie/laravel-permission`. Roles (e.g., `admin`, `billing`, `support`) are assigned per tenant user. Controllers and Blade views gate sensitive actions with `$this->authorize()` or `@can` directives.

---

## Tenant Isolation

### Application Layer
`TenantScope` is a global Eloquent scope registered on all tenant-owned models via the `BelongsToTenant` trait. It automatically appends `WHERE tenant_id = ?` to every query. The current tenant is resolved from the authenticated user and bound in the container by `TenantMiddleware`:

```php
app()->instance('currentTenant', $tenant);
```

`withoutGlobalScopes()` must only be used in platform-admin contexts or cross-tenant reporting, and must be documented inline with a comment explaining the intent.

### Database Layer
Every tenant-owned table has a `tenant_id` column with a foreign key constraint referencing `tenants.id`. The database will reject any insert or update that references a non-existent tenant, providing a last-line-of-defence beneath the application layer.

---

## CSRF Protection

All state-changing requests (POST, PUT, PATCH, DELETE) are protected by Laravel's `VerifyCsrfToken` middleware. Blade forms must always include:

```blade
<form method="POST" ...>
    @csrf
    @method('PUT')  {{-- for non-POST verbs --}}
    ...
</form>
```

Ajax requests must include the `X-CSRF-TOKEN` header. The token is available at `document.head.querySelector('meta[name="csrf-token"]').content`.

---

## Sensitive Data

| Data | Storage | Notes |
|---|---|---|
| Passwords | bcrypt hash | Never stored in plain text |
| Payment transaction references | Plain text | Transaction IDs from bKash/Nagad/cash receipts; acceptable for ISP operations |
| Card data | Not stored | No card payments in scope; no PCI-DSS obligation |
| SMS content | Log record only | Message text stored in `sms_logs`; no provider credentials in the database |

---

## Recommendations

The following security improvements are recommended before production use at scale:

1. **Login rate limiting** — apply Laravel's built-in `throttle` middleware to the login route:
   ```php
   Route::post('/login', ...)->middleware('throttle:5,1');
   ```
   This limits login attempts to 5 per minute per IP.

2. **Two-factor authentication for platform admins** — platform super-admins have access to all tenant data. 2FA (e.g., via `laravel/fortify` TOTP) is strongly recommended for these accounts.

3. **HTTPS enforcement in production** — force HTTPS by adding to `AppServiceProvider::boot()`:
   ```php
   if (app()->environment('production')) {
       URL::forceScheme('https');
   }
   ```
   Also set `SESSION_SECURE_COOKIE=true` in the production `.env`.

4. **File permission hardening** — ensure `storage/` and `bootstrap/cache/` are writable by the web server user only:
   ```bash
   chmod -R 775 storage bootstrap/cache
   chown -R www-data:www-data storage bootstrap/cache
   ```

5. **`.env` file protection** — confirm `.env` is not web-accessible. Nginx/Apache must deny access to dot-files in the project root. The document root should point to `public/` only.

---

## SQL Injection

Not applicable. The application uses Laravel's Eloquent ORM and the query builder throughout. All user-supplied values are passed as PDO bound parameters — never interpolated into raw SQL strings. There are no raw `DB::statement()` or `DB::select()` calls with unbound user input in the codebase.
