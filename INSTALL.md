# Aurave ERP – Installation Steps

## Prerequisites

- XAMPP (or LAMP): Apache, MySQL, PHP 7.4+
- Browser

## Step 1: Place project

Copy or clone the project to your web root, e.g.:

- **XAMPP:** `C:\xampp\htdocs\Aurave` (Windows) or `/opt/lampp/htdocs/Aurave` (Linux)
- Ensure the folder contains `app/`, `public/`, `database/`, `.env`, etc.

## Step 2: Create database

1. Start Apache and MySQL (XAMPP Control Panel).
2. Open phpMyAdmin: `http://localhost/phpmyadmin`
3. Create a new database: `aurave_erp` (or another name; set it in `.env`).
4. Select the database, go to **Import**.
5. Import in this order:
   - **First:** `database/schema.sql` (creates all tables).
   - **Second:** `database/seed.sql` (SaaS-safe: Super Admin, demo company, categories, chart of accounts only).

## Step 3: Configure environment

1. In the project root, ensure `.env` exists (copy from `.env.example` if needed).
2. Edit `.env` and set:
   - `DB_NAME=aurave_erp` (or your database name)
   - `DB_USER=root` (or your MySQL user)
   - `DB_PASS=` (your MySQL password; empty for default XAMPP)
   - `APP_URL=http://localhost/Aurave/public`  
     Change the path if you use a different folder or virtual host.

## Step 4: Run the application

**Option A – Document root on `public` (recommended)**

1. Point your vhost DocumentRoot to the `public` folder, e.g.  
   `C:\xampp\htdocs\Aurave\public`
2. Open: `http://localhost` (or your vhost URL).

**Option B – Subfolder**

1. Open: `http://localhost/Aurave/public`
2. If you get 404, ensure Apache `mod_rewrite` is enabled and `.htaccess` is allowed.

## Step 5: First login and new tenants

**Super Admin (seed) — LOCAL / DEMO ONLY (do not use in production):**

| Email                    | Password   |
|--------------------------|------------|
| superadmin@system.local | **password** |

Change this password on first login. This is the only seeded user (SaaS-safe).

**New tenants:** Use **Register** to create a new company. Enter your name, email, **company name**, and password. After registration, log in with the same email/password; you will be the **admin** of your new company. Create employees, customers, and products via the UI.

## Troubleshooting

- **Blank page:** Enable `APP_DEBUG=1` in `.env` and check PHP/MySQL errors in logs.
- **404 on all routes:** Enable `mod_rewrite` and use the correct `APP_URL` and RewriteBase (in `public/.htaccess`: `RewriteBase /Aurave/public/`).
- **Database connection failed:** Check `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS` in `.env` and that MySQL is running.
- **Session/login issues:** Ensure PHP session path is writable and cookies are enabled.
