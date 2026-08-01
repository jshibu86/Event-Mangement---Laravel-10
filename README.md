# Festiva — Event Management Platform

Laravel 10 scaffold for Games and Programs masters, event timelines, derived budgets, PDF agendas, and historical ratings.

## Setup

1. Copy `.env.example` to `.env`.
2. Configure your database (MySQL or SQLite).
3. Run `php artisan key:generate`.
4. Run `composer install`.
5. Run `npm install && npm run build`.
6. Run `php artisan migrate --seed`.
7. Run `php artisan storage:link`.
8. Start the application with:

    ```bash
    php artisan serve
    ```

---

## Database Configuration

### Option 1: MySQL (Default)

Update your `.env` file:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=event_management
DB_USERNAME=root
DB_PASSWORD=
```

---

### Option 2: SQLite

1. Create an empty SQLite database file:

```text
database/database.sqlite
```

2. Update your `.env` file:

```env
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
```

> **Note:** If your project does not automatically resolve relative paths, use the full absolute path instead.

Example (Windows):

```env
DB_CONNECTION=sqlite
DB_DATABASE=D:/Projects/Festiva/database/database.sqlite
```

3. Clear the configuration cache:

```bash
php artisan config:clear
php artisan cache:clear
```

4. Run the migrations and seeders:

```bash
php artisan migrate --seed
```

---

## Demo Login

**Local URL:** `http://127.0.0.1:8000`

After running the migrations and seeders, sign in with:

- **Email:** `admin@festiva.test`
- **Password:** `password`

> **Important:** Change the default password before using the application outside of a local development environment.

---

## Demo Data

The seeders include sample data for **Onam Celebration 2026**.

You can:

- Create Games and Program Masters.
- Build an event agenda.
- Add and verify expenses.
- Lock the event after verification.
- Generate Guest and Internal PDF agendas.
- Review historical event ratings.
