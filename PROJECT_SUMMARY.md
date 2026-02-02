# Aurave ERP – Project Summary

**Electronics & IT Industry ERP** – Multi-tenant, SaaS-ready web application. Built with PHP (OOP, MVC), MySQL, Bootstrap 5.

---

## Quick Overview

| Item | Details |
|------|---------|
| **Stack** | PHP 7.4+, MySQL 5.7+, Bootstrap 5 |
| **Architecture** | MVC, multi-tenant (company_id), role-based |
| **Entry** | `public/index.php` |
| **Config** | `.env` (copy from `.env.example`) |

---

## Roles & Access

| Role | Display | Access |
|------|---------|--------|
| **super_admin** | System Admin | All companies, Companies CRUD |
| **admin** | Vendor | Full ERP for own company, Orders (OMS) |
| **staff** | Staff | ERP for own company |
| **customer** | Customer | Store, cart, checkout, My Orders |

---

## Features

- **Auth:** Login, register, logout, profile, change password
- **Inventory:** Products CRUD, categories, suppliers, stock, low-stock alerts
- **Production:** BOM, production orders
- **Sales:** Invoices, customer list
- **Purchasing:** Purchase orders
- **Accounting:** Chart of accounts, journal, P&L
- **Payroll:** Employees, payroll runs, payslips
- **E-commerce:** Store, cart, checkout, orders (OMS)
- **Profile:** Per-role profile page (customer/vendor/admin)

---

## Data Flow (IDs & Foreign Keys)

- **users** → company_id (FK), role
- **customers** → company_id, user_id (FK)
- **orders** → company_id, customer_id, user_id
- **products** → company_id, category_id, supplier_id
- Orders go to **product's company** (vendor), not customer's company

---

## File Structure

```
app/
├── config/       app.php, database.php
├── controllers/  14 controllers
├── core/         Database, Model, Controller, Router
├── middlewares/  Auth, Guest, Admin, Staff, SuperAdmin
├── models/       24 models
├── views/        auth, dashboard, store, profile, etc.
└── routes.php
public/
├── index.php     Entry point
├── .htaccess     Rewrite rules
└── assets/       CSS, JS
database/
├── schema.sql    Full MySQL schema
└── seed.sql      Demo data (INSERT IGNORE)
```

---

## Default Login (Demo Only)

| Email | Password |
|-------|----------|
| superadmin@system.local | password |

---

## Pre-Deploy Checklist

- [x] `.env` in `.gitignore`
- [x] No secrets in code
- [x] PHP syntax OK
- [x] README, SECURITY.md, LICENSE present
