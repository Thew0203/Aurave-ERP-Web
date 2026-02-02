# SaaS Safety & Best Practices

This document explains why the refactored seed data and authentication flow are safe for public use, GitHub, and free hosting.

---

## 1. Seed Data Rules (GitHub-Safe)

### What Is Seeded

| Data | Purpose | Safe? |
|------|---------|--------|
| **Super Admin** (1 user) | Platform administration only. Email: `superadmin@system.local` (system/localhost only). | ✅ No real identity |
| **Demo Company** (1 row) | Optional placeholder tenant; no users attached. Name "Demo Company", slug "demo". | ✅ No personal data |
| **Categories** (3 rows) | Generic: General, Office, Raw Materials. Scoped to `company_id = 1` (demo). | ✅ No PII |
| **Chart of Accounts** (8 rows) | Standard COA for demo company. New tenants get their own COA on registration. | ✅ No PII |

### What Is NOT Seeded

- **No employees** – Created only via Payroll UI after login.
- **No customers** – Created only via Sales/Customers UI.
- **No suppliers** – Created only via Inventory UI.
- **No products** – Created only via Inventory UI.
- **No extra users** – No pre-created admin/staff/customer accounts. Only Super Admin (system) exists.

### Why This Is Safe

- **No real-looking names** – No "John Doe", "Jane Smith", or real emails (except `superadmin@system.local`).
- **No sensitive data** – Default password is documented and must be changed on first login.
- **Safe to commit** – `database/seed.sql` contains no PII, no real emails (except system), no fake employee/customer records.

---

## 2. Authentication Flow (Real-World SaaS)

### Registration (Anyone Can Register)

1. User submits: **Name**, **Email**, **Company name**, **Password** (optional: phone, address).
2. System checks: **Email globally unique** (one account per email).
3. System creates:
   - **New company (tenant)** with unique slug derived from company name.
   - **New user** linked to that company with role **admin**.
   - **Default chart of accounts** for the new company (Cash, AR, Inventory, AP, Equity, Revenue, COGS, Operating Expenses).
4. User is not auto-logged in; they use **Login** with the same email/password.

### Login

- **Super Admin** – Email `superadmin@system.local`, password as in README (change on first login).
- **Any registered user** – Lookup by email globally; one match per email. Role and `company_id` from that user record.
- **Role-based redirects** – Unchanged (super_admin → dashboard/super, admin → dashboard/admin, staff → dashboard, customer → store).

### Why This Is SaaS-Safe

- **Self-service signup** – No manual user/company creation required for new tenants.
- **Strict tenant isolation** – Every record is scoped by `company_id`; no shared data between companies.
- **One email = one account** – Prevents duplicate accounts and simplifies login (no company picker needed).

---

## 3. Employees & Customers (UI-Only)

- **Employees** – Created only via **Payroll → Employees** (Add Employee). No seed employees.
- **Customers** – Created only via **Sales → Customers** or when needed for sales. No seed customers.
- **CRUD** – All modules (Inventory, Sales, Purchasing, Payroll, etc.) work after login; data is created through the UI and always respects `company_id`.

---

## 4. Database Safety Checklist

- [x] `seed.sql` has no real personal names.
- [x] No real email addresses (except `superadmin@system.local` for system use).
- [x] No fake employee or customer records.
- [x] Default system password documented; change on first login.
- [x] Safe to commit to GitHub and use on free hosting.

---

## 5. Multi-Tenant Guarantees

- **company_id** – All tenant-scoped tables (products, sales, orders, employees, etc.) have `company_id` and use it in queries.
- **Session** – On login, `$_SESSION['company_id']` is set from the user’s record; all subsequent reads/writes use this.
- **Super Admin** – Has `company_id = NULL` and can manage all companies; normal users never see other tenants’ data.
- **New registration** – Creates a new company and assigns the user as admin of that company only.

---

## 6. Confirmation: System Still Works After Deployment

1. **Deploy** – Import `schema.sql`, then `seed.sql`. Configure `.env`.
2. **Super Admin** – Log in with `superadmin@system.local` / default password (see README). Change password immediately. Access **Dashboard → Super** and **Companies**.
3. **New tenant** – Open **Register**, enter name, email, **company name**, password. Submit.
4. **New admin** – Log in with the same email/password. Redirected to **Dashboard** (admin). Create products, categories, customers, employees, sales, etc. via UI.
5. **Isolation** – Register a second tenant with a different email and company name. Log in as second admin; only that company’s data is visible. No cross-tenant data leak.

All features (Inventory, Production, Sales, Purchasing, Accounting, Payroll, Orders/OMS, Store) work after login; data is created only through the UI and respects `company_id`.
