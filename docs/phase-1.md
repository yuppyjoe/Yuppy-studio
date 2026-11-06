# Phase 1: Foundation & Architecture

## What was built
- **Laravel 13 app** with Livewire 3 and Volt, Alpine.js, Tailwind CSS 3 via Vite, and MySQL. Breeze (Livewire/Volt) handles register, login, logout, password reset and email verification.
- **Roles**: `admin`, `photographer`, `customer` on `users`, plus `status` and `last_login_at`. Photographers and customers choose their role when they register; `admin` is refused and admins come from the seeder. `role` and `status` are not mass-assignable.
- **Schema** with foreign keys and indexes: photographer_profiles, customers, branding_profiles, occasions, design_families, book_formats, manufacturer_templates (all measurements nullable, `is_verified = false`), photobook_projects (a stub with soft deletes), ai_requests (empty) and audit_logs.
- **Authorisation**: `role:` middleware on every area, and policies for PhotographerProfile, BrandingProfile, Customer and PhotobookProject. Every owned model has a `visibleTo($user)` scope that lists use. Suspended users can't log in and are signed out if suspended mid-session.
- **BrandingResolver**: a photographer's project never resolves to Yuppy branding. This is also enforced when a project is saved.
- **Private files**: logos are stored on the `private` disk and served only by `GET /files/branding/{id}/logo` after a policy check. Only PNG, JPG and WebP are accepted, up to 2 MB, 64 to 6000 px. SVG is refused.
- **AuditLogger**: records login, logout, registration, account changes, studio profile and branding changes (with old and new values), logo changes, and client create/update/delete. Passwords are never logged.
- **UI**: one app shell for all roles (sidebar and top bar, slide-out drawer on mobile). Design tokens are in `tailwind.config.js`. Fonts are self-hosted: Cormorant Garamond for headings, Inter for body text. Components live in `resources/views/components/ui`: button, card, input, select, textarea, file upload, modal, toast, badge, empty state, stat and page header.
- **Pages**
  - Admin: overview (counts and recent activity), Yuppy branding, photographers and customers (both read-only).
  - Photographer: overview with a setup checklist, studio and branding, clients (add, edit, remove), projects (list and detail).
  - Customer: my photobooks and photobook detail.
  - Everyone: account settings.
- **Seeders** (idempotent): 8 occasions, 5 design families, 6 book formats, the Yuppy brand with a placeholder logo, an admin, 2 demo photographers with branding and 2 demo customers. No manufacturer templates are seeded.

## How to run

```bash
composer install
npm install
cp .env.example .env && php artisan key:generate

# MySQL: create the database, then set DB_USERNAME / DB_PASSWORD in .env
mysql -u <user> -p -e "CREATE DATABASE yuppy_studio CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"

php artisan migrate --seed
npm run build            # or: npm run dev
php artisan serve        # http://localhost:8000
php artisan queue:work   # database queue (verification and reset emails)
```

Demo accounts (local only, password `password`):

| Role | Email |
|---|---|
| Admin | admin@yuppy.test (override with `SEED_ADMIN_EMAIL` / `SEED_ADMIN_PASSWORD`) |
| Photographer | amani@studio.test, baraka@studio.test |
| Customer | zawadi@customer.test, kevin@customer.test |

Mail uses the `log` driver locally, so verification and reset links appear in `storage/logs/laravel.log`. Seeded accounts are already verified.

**Production:** set `SEED_ADMIN_PASSWORD` before seeding, or the admin is not created. Demo accounts are never seeded in production.

## How to test

```bash
php artisan test                                    # Pest (SQLite in-memory, no MySQL needed)
vendor/bin/pint --test                              # code style
vendor/bin/phpstan analyse --memory-limit=1G        # Larastan level 5
```

Current result: **96 tests, 268 assertions, all passing**; Pint clean; Larastan level 5 with 0 errors.

| Requirement | Test file |
|---|---|
| Each role lands on its own dashboard | `tests/Feature/RoleDashboardTest.php` |
| Customer gets 403 on admin and photographer routes (and the other cross-role cases) | `tests/Feature/RoleAccessTest.php` |
| Photographer A can't view, edit, list or download B's profile, branding, customers, projects or logo | `tests/Feature/PhotographerIsolationTest.php` |
| Logo upload rejects invalid types (PDF, SVG, disguised script) and oversize files | `tests/Feature/LogoUploadTest.php` |
| BrandingResolver never returns Yuppy branding for a photographer project | `tests/Feature/BrandingResolverTest.php` |
| Seeders produce 8 occasions, 5 families, 6 formats and 0 verified templates | `tests/Feature/SeederTest.php` |
| Audit entries for login, logout and profile/branding changes | `tests/Feature/AuditLogTest.php` |
| Registration roles (admin refused), suspended accounts | `tests/Feature/Auth/*` |

## Known gaps (by design or deferred)
- Projects can't be created from the UI yet (`PhotobookProjectPolicy::create` returns false). That arrives with the Design Engine. Lists and detail pages work for projects that exist.
- Admin lists are read-only: no suspending users or changing roles from the UI yet (Phase 10).
- Logos are validated and sniffed by MIME type, but not re-encoded to strip metadata. Consider re-encoding when the image pipeline lands (Phase 3).
- The audit log has no admin page beyond the overview's recent activity feed.
- Tests run on SQLite. Run `php artisan migrate:fresh --seed` against MySQL once to confirm everything works there.

## Next task: Phase 2, Design Engine
Model books as data: spreads, pages, frames and slots, plus layout templates per design family and book format. Let photographers create and configure projects (occasion, format, family, branding mode, client), then choose the editor library behind an adapter. See `docs/roadmap.md`.
