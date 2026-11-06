# Yuppy AI Photobook Designer: Architecture

## 1. What was found (Step 0)

| Item | Found |
|---|---|
| Repository | Empty apart from `README.md` and the `Phase1` brief (branch `book-phase-1`). No Laravel app. |
| PHP | 8.3.11 with pdo_mysql, pdo_sqlite, gd, imagick, intl, fileinfo, mbstring, zip, bcmath |
| Composer | 2.8.6 |
| Node / npm | 22.18 / 10.9 |
| MySQL | 8.0 server, running locally |

Plan: create a fresh Laravel app in place, install Breeze (Livewire/Volt stack) and the
tooling, then build Phase 1 in commit-sized steps with tests after each step.

## 2. Stack

| Layer | Choice | Notes |
|---|---|---|
| Framework | Laravel 13 | The current release. The brief says "Laravel 11 or the compatible version"; 11 no longer gets security fixes. |
| UI | Livewire 3 + Volt, Alpine.js (bundled with Livewire), Tailwind CSS 3 via Vite | Tailwind 3 keeps design tokens in `tailwind.config.js`, as the brief asks. |
| Auth | Laravel Breeze (Livewire/Volt) | register, login, logout, password reset, email verification |
| Database | MySQL 8 (development/production); SQLite in-memory for the test suite | |
| Queue | `database` driver | `jobs` table ships with the skeleton |
| Files | `private` disk (`storage/app/private`), default disk | Never web-served; streamed through policy-checked routes |
| Fonts | Self-hosted via `@fontsource`: Cormorant Garamond (headings), Inter (body) | Bundled by Vite, no third-party CDN |
| Quality | Pest 4, Laravel Pint, Larastan level 5 | |

Not installed on purpose: Konva, Fabric or any canvas/editor library, AI SDKs, payment SDKs.

## 3. Domain model

```
users (role: admin|photographer|customer, status: active|suspended, last_login_at)
 ├─1:1─ photographer_profiles (studio identity)
 │        ├─1:1─ branding_profiles (what appears in books: brand name, contacts, socials, logo)
 │        ├─1:n─ customers (the photographer's clients)
 │        └─1:n─ photobook_projects
 ├─1:1─ customers (a self-registered customer's own record, user_id set)
 ├─1:n─ audit_logs (actor)
 └─1:n─ ai_requests (empty in Phase 1)

branding_profiles.photographer_profile_id = NULL  →  the single Yuppy house brand
occasions, design_families, book_formats          →  reference data (seeded, not hard-coded)
manufacturer_templates → book_formats             →  measurements nullable, is_verified=false, never seeded
photobook_projects (stub): owner_type yuppy|photographer|customer, branding_mode, soft deletes
```

### Ownership rules
- A **photographer** owns their profile, branding profile, customers and projects (all keyed by `photographer_profile_id`).
- A **customer** owns their own `customers` row (`user_id`) and sees projects linked to it.
- An **admin** sees everything. In Phase 1, admin access to photographers and customers is read-only.

## 4. Authorisation

1. **Role middleware** (`role:admin`, `role:photographer`, `role:customer`) guards each area's route group. It returns 403 for the wrong role.
2. **Policies** (`PhotographerProfilePolicy`, `BrandingProfilePolicy`, `CustomerPolicy`, `PhotobookProjectPolicy`) decide every record-level action. Views and components call `can`/`authorize`; they never check `role` directly.
3. **Owner-filtered lists**: every model with an owner has a `visibleTo(User $user)` scope. List pages always query through it, so a photographer's list cannot include another studio's rows even if a policy check were forgotten.
4. **Private files**: logos are stored on the `private` disk and served by `GET /files/branding/{brandingProfile}/logo`. That route requires a logged-in user and runs the `viewLogo` policy check. Responses send `nosniff` and `private` cache headers.

### Branding rule
`App\Services\BrandingResolver::resolve(PhotobookProject)` returns the branding a project prints with:

| Project | Result |
|---|---|
| Photographer-owned, or any project with a `photographer_profile_id` | That photographer's branding (when `branding_mode = photographer`), otherwise none. **Never Yuppy.** |
| Yuppy- or customer-owned, `branding_mode = yuppy` | The Yuppy house brand |
| Anything else | None (unbranded) |

The model also refuses to save a photographer project with `branding_mode = yuppy`.

## 5. Application layout

```
app/
  Enums/            UserRole, UserStatus, ProjectOwnerType, BrandingMode, ProjectStatus
  Http/Middleware/  EnsureUserHasRole
  Http/Controllers/ BrandingLogoController (private file streaming), DashboardRedirectController
  Listeners/        RecordLogin, RecordLogout (audit + last_login_at)
  Models/           one per table
  Policies/         see above
  Services/         AuditLogger, BrandingResolver, LogoStorage
resources/views/
  layouts/app.blade.php      shared shell (sidebar + top bar, collapsible on mobile)
  components/ui/*            design-system components
  livewire/pages/{admin,studio,customer}/*   Volt full-page components per area
```

### Areas and routes
| Area | Prefix | Pages |
|---|---|---|
| Admin | `/admin` | dashboard, Yuppy branding, photographers (read-only), customers (read-only) |
| Photographer | `/studio` | dashboard, studio & branding profile, clients (CRUD), projects (read-only list/detail) |
| Customer | `/my` | dashboard (my photobooks), project detail |
| Shared | `/dashboard` redirects to the right area; `/profile` is the account settings page | |

## 6. Audit trail
`App\Services\AuditLogger` writes to `audit_logs`: actor, event, subject (polymorphic), old and new values, IP address and user agent. Phase 1 events: `auth.login`, `auth.logout`, `auth.registered`, `account.updated`, `photographer_profile.updated`, `branding_profile.updated`, `branding_profile.logo_updated`, `customer.created`, `customer.updated`, `customer.deleted`.

## 7. Out of scope for Phase 1
AI generation, photo uploads, the editor and renderer, orders and payments, and manufacturer measurements (only an admin can enter and verify those, in a later phase).
