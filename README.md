# Yuppy-studio
This is a repo for Yuppy Enterprises studio.

**Yuppy AI Photobook Designer**: Laravel 13 · Livewire 3 (Volt) · Alpine.js · Tailwind CSS · MySQL.

- [Phase 1: what was built, how to test, what's next](docs/phase-1.md)
- [Architecture](docs/architecture.md)
- [Roadmap (10 phases)](docs/roadmap.md)

---

## Setting up the project

### 1. Prerequisites

| Tool | Version | Check with |
|---|---|---|
| PHP | 8.3 or newer, with extensions `pdo_mysql`, `mbstring`, `fileinfo`, `gd`, `intl`, `zip`, `bcmath` | `php -v` and `php -m` |
| Composer | 2.x | `composer --version` |
| Node.js / npm | Node 20 or newer | `node -v` |
| MySQL | 8.0 | `mysql --version` |
| Git | any recent version | `git --version` |

### 2. Get the code

```bash
git clone git@github.com:yuppyjoe/Yuppy-studio.git
cd Yuppy-studio
git checkout develop        # develop is the working branch
```

### 3. Install dependencies

```bash
composer install
npm install
```

### 4. Create your environment file

```bash
cp .env.example .env
php artisan key:generate
```

### 5. Create the MySQL database and user

Log in to MySQL as an admin user (for example `sudo mysql` or `mysql -u root -p`) and run:

```sql
CREATE DATABASE yuppy_studio CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'appuser'@'localhost' IDENTIFIED BY 'choose-a-strong-password';
GRANT ALL PRIVILEGES ON yuppy_studio.* TO 'appuser'@'localhost';
FLUSH PRIVILEGES;
```

Then put the same details in `.env`:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=yuppy_studio
DB_USERNAME=appuser
DB_PASSWORD="choose-a-strong-password"
```

> Wrap the password in double quotes if it contains special characters such as `*`, `@`, `#` or spaces.
> `.env` is git-ignored. Never commit real credentials.

### 6. Create the tables and demo data

```bash
php artisan migrate --seed
```

This creates all tables and seeds:
- the reference data: 8 occasions, 5 design families and 6 book formats
- the Yuppy brand, with a placeholder logo
- an admin account
- outside production only: 2 demo photographers and 2 demo customers

To start again from a clean database at any time, run `php artisan migrate:fresh --seed`.

### 7. Build the front-end assets

```bash
npm run build      # one-off build
# or, while developing (auto-reloads on changes):
npm run dev
```

### 8. Run the app

```bash
php artisan serve          # http://localhost:8000
php artisan queue:work     # optional: in a second terminal, to process queued jobs
```

Open **http://localhost:8000**.

---

## Demo accounts (local only)

These accounts are created by the seeder outside production. The password for all of them is `password`.

| Role | Email | Lands on |
|---|---|---|
| Admin | `admin@yuppy.test` | `/admin` |
| Photographer | `amani@studio.test`, `baraka@studio.test` | `/studio` |
| Customer | `zawadi@customer.test`, `kevin@customer.test` | `/my` |

You can also register at `/register` as a photographer or a customer. Admins cannot self-register.

**Emails:** locally, mail uses the `log` driver. Email-verification and password-reset links are written to `storage/logs/laravel.log` instead of being sent. Seeded accounts are already verified.

---

## Running the checks

```bash
php artisan test                               # Pest test suite (uses in-memory SQLite, no MySQL needed)
vendor/bin/pint --test                         # code style (run `vendor/bin/pint` to fix)
vendor/bin/phpstan analyse --memory-limit=1G   # static analysis (Larastan level 5)
```

Run all three before opening a pull request.

---

## Production notes

- Set `APP_ENV=production` and `APP_DEBUG=false`.
- Set `SEED_ADMIN_EMAIL` and `SEED_ADMIN_PASSWORD` before running `php artisan db:seed`. Without a password the admin account is not created, and demo accounts are never seeded in production.
- Configure a real mail driver (`MAIL_MAILER`, `MAIL_HOST`, etc.) so verification and reset emails are delivered.
- Run a queue worker under a process manager (e.g. Supervisor): `php artisan queue:work`.
- Run `npm run build` and `php artisan optimize` on deploy.
- User files (logos) are stored on the private disk in `storage/app/private`. Make sure `storage/` is writable and is not web-accessible.

---

## Troubleshooting

| Problem | Fix |
|---|---|
| `SQLSTATE[HY000] [1045] Access denied` | Check `DB_USERNAME` / `DB_PASSWORD` in `.env` (quote the password), then run `php artisan config:clear`. |
| `SQLSTATE[HY000] [1049] Unknown database` | Create the database (step 5). |
| Page loads without styling, or a `Vite manifest not found` error | Run `npm run build`, or keep `npm run dev` running. |
| Changes to `.env` are ignored | Run `php artisan config:clear`. |
| Logo upload fails | Make sure `storage/` is writable: `chmod -R ug+rw storage bootstrap/cache`. |

## Branches

- `develop`: the default working branch. Branch off it for features and open pull requests back into it.
- `main`: stable releases.
