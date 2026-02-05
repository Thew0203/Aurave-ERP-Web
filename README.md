# Aruave – Electronics & IT Industry ERP

A complete, business-ready web ERP for the **electronics and IT industry** (Arduino, networking, computers, components & more). Integrated e-commerce and order management. Built with PHP (OOP, MVC), MySQL, Bootstrap 5, and vanilla JavaScript. Multi-tenant (SaaS-ready), animated UI, and XAMPP/LAMP compatible.

---

## System Overview

- **Backend:** PHP 7.4+ (OOP, MVC)
- **Database:** MySQL 5.7+ (phpMyAdmin compatible)
- **Frontend:** HTML5, CSS3, Bootstrap 5, vanilla JavaScript
- **Auth:** Secure login/registration, password hashing (`password_hash`), sessions, role-based access
- **Architecture:** Multi-tenant (company_id), modular, scalable

### Roles

| Role         | Access |
|-------------|--------|
| Super Admin | All companies (tenants), system settings |
| Admin       | Full ERP for own company |
| Staff       | ERP modules for own company (no company management) |
| Customer    | Store, cart, checkout, order history |

---

## Increment / Decrement (Stock & Orders)

Product **quantity** is updated automatically across the system:

| Action | Effect on product quantity |
|--------|----------------------------|
| **Store checkout** | **Decremented** – each ordered item deducts from product stock; stock movement (sale) is recorded. |
| **Sales (ERP)** | **Decremented** – creating a sale deducts product quantity and records stock movement. |
| **Purchasing** | **Incremented** – creating a purchase adds product quantity and records stock movement. |
| **Inventory → Stock movement** | **In** = increment, **Out** = decrement, **Adjustment** = add or subtract. |
| **Production (complete order)** | **Decremented** for raw materials (BOM consumption), **Incremented** for finished product. |

Cart and checkout enforce **stock limits**: add-to-cart and cart update cap quantity to available stock; checkout fails with a clear message if any item has insufficient stock.

---

## Product Fields & Functions

| Field | Function / validation |
|-------|------------------------|
| **Product Image** | Optional upload (JPEG/PNG/GIF/WebP, max 2MB). Shown in store and product list. |
| **SKU** | Optional code for your reference. |
| **Name** | **Required.** Displayed in store, sales, orders, low-stock alerts. |
| **Description** | Optional. Shown on store product page. |
| **Category** | Optional. Used for filtering/organization (e.g. Arduino, Networking). |
| **Supplier** | Optional. Links to suppliers list. |
| **Unit** | Default `pcs`. Used in displays. |
| **Cost Price** | Must be ≥ 0. Used for inventory value and margin. |
| **Selling Price** | Must be ≥ 0. **Used in store, cart, checkout, and sales.** You can edit anytime (Edit Product). |
| **Quantity** | Must be ≥ 0. **Decremented on order/sale; incremented on purchase/stock-in.** |
| **Low Stock Threshold** | Must be ≥ 0. When quantity ≤ this, product appears in **Low Stock Alerts**. |
| **Raw Material** | If checked, product is not shown in store; used in BOM/production only. |
| **Active** | If unchecked (edit only), product is hidden from store and sellable lists. |

Validation on create/update: name required; cost price, selling price, quantity, and low stock threshold must be ≥ 0. Errors are shown on the form and fields are repopulated.

---

## Folder Structure

```
/Aurave
├── app/
│   ├── config/          # database.php, app.php
│   ├── controllers/     # Auth, Dashboard, Inventory, Production, Sales, Purchasing, Accounting, Payroll, Order, Company, Store
│   ├── core/            # Database (PDO), Model, Controller, Router
│   ├── middlewares/     # Auth, Guest, SuperAdmin, Admin, Staff
│   ├── models/          # User, Company, Product, Sale, Order, etc.
│   ├── views/           # Layout, auth, dashboard, inventory, sales, store, ...
│   └── routes.php       # All routes
├── database/
│   ├── schema.sql      # Full MySQL schema
│   └── seed.sql        # SaaS-safe seed (Super Admin, demo company, COA, categories only)
├── docs/
│   ├── SAAS_SAFETY.md           # SaaS safety and registration flow
│   └── GITHUB_RELEASE_CHECKLIST.md  # Public-release checklist
├── public/
│   ├── index.php       # Entry point
│   ├── .htaccess       # Rewrite to index.php
│   └── assets/         # css/app.css, js/app.js
├── .env                 # Environment config (DB, mailer). Create from README.
├── composer.json
└── README.md
```

---

## Installation

### 1. Environment

- XAMPP (or LAMP): Apache + MySQL + PHP 7.4+.
- Place the project under the web root (e.g. `htdocs/Aurave`).

### 2. Database

1. Create a database, e.g. `aurave_erp`.
2. In phpMyAdmin (or MySQL client):
   - Import `database/schema.sql`.
   - Then import `database/seed.sql`.
   - For login security emails, import `database/migrations/security_mailer.sql`.

### 3. Configuration

1. Create a `.env` file in the project root with:

   **Required (app & database):**
   - `APP_NAME=Aurave ERP`
   - `APP_ENV=development`
   - `APP_DEBUG=1`
   - `APP_URL=http://localhost/Aurave/public`
   - `DB_HOST=localhost`
   - `DB_NAME=aurave_erp`
   - `DB_USER=root`
   - `DB_PASS=`
   - `DB_CHARSET=utf8mb4`
   - `SESSION_LIFETIME=7200`
   - `DEFAULT_TIMEZONE=UTC`

   **Login security mailer (sends “You logged in” to every user who logs in):**
   - `MAILER_ENABLED=1`
   - `MAILER_FROM_EMAIL=` your sending Gmail (e.g. `you@gmail.com`)
   - `MAILER_FROM_NAME=Aurave`
   - `SMTP_HOST=smtp.gmail.com`
   - `SMTP_PORT=587`
   - `SMTP_USER=` same as MAILER_FROM_EMAIL
   - `SMTP_PASS=` Gmail App Password (16 chars, no spaces)

   **If login emails don’t arrive:** (1) Import `database/migrations/security_mailer.sql`. (2) Set `APP_DEBUG=1` in .env and check PHP error log for "SecurityMailer SMTP:" or "SecurityMailer:". (3) Confirm the user’s email in the `users` table. (4) Restart Apache after changing .env.

### 4. Web Server (Apache)

Ensure `mod_rewrite` is enabled. The app is intended to run from the `public` folder:

- **Document root:** point the vhost to `Aurave/public`,  
  **or**
- **URL:** `http://localhost/Aurave/public` (with `.htaccess` RewriteBase `/Aurave/public/`).

### 5. Composer (optional)

If you use Composer:

```bash
cd Aurave
composer install
```

If you don’t run Composer, the project includes a simple PSR-4 autoload fallback in `public/index.php`.

---

## Default Credentials (from seed) — **LOCAL / DEMO ONLY**

> **Do not use these credentials in production.** For local development and demo only. Change the Super Admin password on first login.

The seed creates **only one user** (Super Admin). No employees, customers, or other users are seeded (SaaS-safe).

| Email                     | Role        | Company   | Password   |
|---------------------------|------------|-----------|------------|
| superadmin@system.local  | super_admin | (global) | **password** (change on first login) |

**New tenants:** Use **Register** to create a new company and become its admin. Registration creates a new company (tenant), assigns you as **admin**, and seeds a default chart of accounts for that company. Then log in with the same email/password.

---

## System Flow

1. **Guests:** Home redirects to Store. **Register** (creates new company + admin) or **Login** from nav.
2. **Registration:** Anyone can register. Provides name, email, **company name**, password. System creates a new tenant (company) and assigns the user as **admin** with a default chart of accounts. No employees or customers are seeded; create them via UI.
3. **Customer:** After login (if role = customer) → Store; Cart; Checkout; My Orders and Order Tracking.
4. **Staff/Admin:** After login → Dashboard (KPIs, recent sales, low stock). Access: Inventory, Production, Sales, Purchasing, Accounting, Payroll, Orders (OMS). Employees and customers are created only via UI.
5. **Super Admin:** Same as Admin plus **Companies** (tenants) and Super Dashboard. Only seed user; email is system-only (`superadmin@system.local`).

---

## Quick test for instructor

1. **Run the app:** Open `http://localhost/Aurave/public` (or your `APP_URL`). You should see the landing page or Store.
2. **Login as Super Admin:** Email `superadmin@system.local`, password `password`.
3. **Add a product:** **Inventory → Products → Add Product.** Fill Name, set **Selling Price** (e.g. 19.99) and **Quantity** (e.g. 10), then Save.
4. **Store & cart:** Open **Store** (or log out and go to Store). Add the product to cart; cart shows "In stock" and caps quantity. Go to **Checkout** and place an order (demo only — no real payment).
5. **Verify increment/decrement:** **Inventory → Products** or **Stock** — product quantity should be reduced. **Inventory → Low Stock Alerts** shows items where quantity ≤ threshold.
6. **Edit product:** **Inventory → Products → Edit** on the product; change **Selling Price** or **Quantity** and Save. Validation: name required; cost/selling price, quantity, and low stock threshold must be ≥ 0.

**Payment note:** This project does **not** integrate Stripe or any real payment gateway. Checkout is **demo-only** (order is recorded, stock is deducted; no card or online payment). For real payments you would add a provider (e.g. Stripe) separately.

---

## Security Notes

- Passwords hashed with `password_hash()` (bcrypt).
- Session-based auth; sensitive routes protected by middlewares.
- Role checks on dashboard and all ERP/OMS routes.
- Inputs escaped in views (`htmlspecialchars`).
- DB access via PDO and parameterized queries.
- For production: use HTTPS, set `APP_DEBUG=0`, restrict DB user, and review file permissions.

---

## Features Implemented

- **Auth:** Login, register, logout, role-based redirects.
- **Login security mailer:** On every login, an email is sent to **that user’s email** (from the database). Sender shows as "Aurave". No hardcoded recipient list; every user gets the alert at their own address.
- **Inventory:** Products CRUD, categories, suppliers, stock in/out, low-stock alerts, valuation.
- **Production:** BOM (header + items), production orders, material consumption, finished goods.
- **Sales:** POS-style sales, customer selection, invoices, sales list.
- **Purchasing:** Purchase orders, goods receiving, purchase list/view.
- **Accounting:** Chart of accounts, journal entries (debit/credit), P&L summary.
- **Payroll:** Employees, salary structure, payroll runs, payslips.
- **E-commerce & OMS:** Store, product page, cart, checkout, order placement, inventory deduction, order status lifecycle (pending → confirmed → processing → shipped → delivered / returned), admin order list/view/status/invoice, customer order history and tracking.
- **Multi-tenant:** Data scoped by `company_id`; Super Admin manages companies.

---

## Before Pushing to GitHub

- Ensure `.env` is not committed (it is in `.gitignore`). If it was ever committed, run `git rm --cached .env`.
- Run: `git grep -E "password|secret|@gmail|1234"` and fix any real secrets before pushing.
- See **[docs/GITHUB_RELEASE_CHECKLIST.md](docs/GITHUB_RELEASE_CHECKLIST.md)** for the full public-release checklist.

## Security

See **[SECURITY.md](SECURITY.md)** for how to report vulnerabilities and what not to disclose publicly.

## License

MIT License. See **[LICENSE](LICENSE)**.
