# Festiva — Event Management Platform

Laravel 10 scaffold for Games and Programs masters, event timelines, derived budgets, PDF agendas, and historical ratings.

## Setup
1. Copy `.env.example` to `.env`, set `DB_*`, then run `php artisan key:generate`.
2. Run `composer install` and `npm install && npm run build`.
3. Run `php artisan migrate --seed` and `php artisan storage:link`.
4. Start with `php artisan serve`.

Seed data includes **Onam Celebration 2026**. Create master items, build an event agenda, add expenses, verify it to lock, and open guest/internal PDFs.

## Demo login

**Local domain:** `http://127.0.0.1:8000` (after running `php artisan serve`)

After running `php artisan migrate --seed`, sign in with:

- **Email:** `admin@festiva.test`
- **Password:** `password`

Change this password before using the application outside a local demo environment.
