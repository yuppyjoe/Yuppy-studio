# Yuppy-studio
This is a repo for Yuppy Enterprises studio.

**Yuppy AI Photobook Designer**: Laravel 13 · Livewire 3 (Volt) · Alpine.js · Tailwind CSS · MySQL.

- [Phase 1: how to run, test, and what's next](docs/phase-1.md)
- [Architecture](docs/architecture.md)
- [Roadmap (10 phases)](docs/roadmap.md)

Quick start: `composer install && npm install && cp .env.example .env && php artisan key:generate`,
set your MySQL credentials in `.env`, then `php artisan migrate --seed && npm run build && php artisan serve`.
