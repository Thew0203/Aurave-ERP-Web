# GitHub-Safe Public Release Checklist

This document confirms that the Aurave ERP project satisfies the GitHub-Safe Public Release Checklist for PHP + MySQL ERP / SaaS systems.

---

## 1. Security & Secrets (Critical)

| Item | Status |
|------|--------|
| No secrets committed | ✅ `.env` and `config.php` in `.gitignore` |
| No database passwords in code | ✅ DB config from `getenv()`; `.env.example` has placeholders only |
| No API keys / SMTP credentials | ✅ None in repo |
| No admin passwords in plain text | ✅ Seed uses bcrypt hash; docs say "change on first login" |
| `.env` file ignored by Git | ✅ Listed in `.gitignore` |
| `.env.example` (no secrets) | ✅ Present with safe defaults and comments |
| All passwords use `password_hash()` | ✅ `User::createUser()` uses `PASSWORD_DEFAULT` |
| No default production passwords | ✅ Demo password documented as **LOCAL ONLY** |

---

## 2. Database & Seed Data

| Item | Status |
|------|--------|
| Seed file public-safe | ✅ No real names, no personal emails, no real phone numbers |
| No hardcoded employees/customers | ✅ None in seed |
| Only system scaffolding | ✅ Super Admin, demo company, chart of accounts, categories |
| Multi-tenant safety | ✅ Tables have `company_id`; queries scoped by tenant |

---

## 3. Authentication & Authorization

| Item | Status |
|------|--------|
| Anyone can register | ✅ Registration form and flow |
| Registration creates User + Company + Admin | ✅ `AuthController::register()` |
| Login works for new users | ✅ `findByEmailGlobal()` used at login |
| Role-based access | ✅ Middlewares: Auth, Guest, SuperAdmin, Admin, Staff |
| Admin-only routes protected | ✅ Middleware on routes |
| Customers cannot access ERP backend | ✅ Role checks and redirects |

---

## 4. Files & Repository Hygiene

| Item | Status |
|------|--------|
| `.gitignore` configured | ✅ `.env`, `config.php`, `/uploads/`, `/logs/`, `/cache/`, `/vendor/` |
| No `*.log`, `.DS_Store`, `.idea/`, `.vscode/` committed | ✅ In `.gitignore` |

---

## 5. Configuration Safety

| Item | Status |
|------|--------|
| Environment-based config | ✅ `app.php` and `database.php` use `getenv()` |
| DB config loaded from env | ✅ No hardcoded credentials |
| Safe defaults | ✅ `.env.example`: `APP_DEBUG=0`, `APP_ENV=production` |
| Error reporting disabled in production | ✅ `public/index.php` sets `display_errors=0`, `log_errors=1` when `env=production` and `debug=0` |

---

## 6. User-Generated Data Handling

| Item | Status |
|------|--------|
| Server-side validation | ✅ Controllers validate required fields, lengths, types |
| SQL safety (PDO prepared statements) | ✅ No raw concatenation; `Model` uses prepared statements |
| XSS protection | ✅ Views use `htmlspecialchars()` for output |

---

## 7. File Upload Safety

| Item | Status |
|------|--------|
| N/A | No file upload feature in current scope; if added later, apply type/size/random-filename rules |

---

## 8. Documentation

| Item | Status |
|------|--------|
| README.md | ✅ Overview, features, tech stack, installation, DB setup, demo credentials (LOCAL ONLY) |
| SECURITY.md | ✅ How to report vulnerabilities; what not to disclose |

---

## 9. Demo & Production Clarity

| Item | Status |
|------|--------|
| Demo credentials labeled "LOCAL ONLY" | ✅ In README and INSTALL |
| No test routes / debug endpoints | ✅ No `/phpinfo.php` or test routes exposed |

---

## 10. Legal & Open Source

| Item | Status |
|------|--------|
| LICENSE file | ✅ MIT License |
| No copyrighted assets | ✅ No third-party assets committed |
| Third-party libraries | ✅ Bootstrap, PDO; credited in README/tech stack |

---

## Final Pre-Push Check

Before pushing, run:

```bash
git status
git grep "password"   # expect only code/docs references, no secrets
git grep "secret"
git grep "@gmail"
git grep "1234"
```

Ensure `.env` is never staged. If any real secret appears, remove it and rotate the secret.

---

**Result:** Public-safe, SaaS-ready, production-minded, and suitable for open-source or portfolio use.
