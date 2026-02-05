# Aurave Security Mailer — 3-Hour Implementation Plan

## ✅ IMPLEMENTATION COMPLETE — Quick Start

1. **Run migration:**
   ```bash
   mysql -u root -p aurave_erp < database/migrations/security_mailer.sql
   ```
   Or in phpMyAdmin: import `database/migrations/security_mailer.sql`

2. **Configure .env** (copy from .env.example):
   ```
   MAILER_ENABLED=1
   MAILER_FROM_EMAIL=noreply@yourdomain.com
   MAILER_FROM_NAME=Aurave Security
   SMTP_HOST=smtp.brevo.com
   SMTP_PORT=587
   SMTP_USER=your_brevo_smtp_login
   SMTP_PASS=your_brevo_smtp_key
   ```

3. **Test:** Log in with a user that has a real email (not superadmin@system.local). Check inbox for "Aurave: You logged in. Is this you?"

---

## 🧭 GOAL
Add a **Login Security Notification** system that:
- Sends a real email when a user logs in
- Email: "You logged in to Aurave. Is this you?"
- Buttons: ✅ "Yes, this was me" | 🚫 "No, this wasn't me"
- Tracks: delivery, opens, clicks, IP, timestamp
- Uses **MailerSend** or **Brevo** (real SMTP + API)

---

## ⏱️ HOUR 1 — Setup & Foundation (60 mins)

### 1.1 Choose Email Provider (15 mins)
| Provider | Pros | Signup |
|----------|------|--------|
| **MailerSend** | Clean API, open/click tracking | [mailersend.com](https://mailersend.com) |
| **Brevo** | Free tier, SMTP + API | [brevo.com](https://brevo.com) |

**Decision:** Pick ONE. Brevo has a generous free tier; MailerSend is simpler.

### 1.2 Environment Variables (.env)
Add to `.env`:
```
# Security Mailer (MailerSend or Brevo)
MAILER_ENABLED=1
MAILER_PROVIDER=mailersend
MAILER_API_KEY=your_api_key_here
MAILER_FROM_EMAIL=noreply@yourdomain.com
MAILER_FROM_NAME=Aurave Security

# SMTP (if using SMTP instead of API)
SMTP_HOST=smtp.brevo.com
SMTP_PORT=587
SMTP_USER=your_smtp_user
SMTP_PASS=your_smtp_pass
```

### 1.3 Database Schema (New Tables)
**File:** `database/migrations/security_mailer.sql` or append to `schema.sql`

```sql
-- Login events (one per login attempt that triggers email)
CREATE TABLE IF NOT EXISTS `login_events` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(500) DEFAULT NULL,
  `device_info` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Verification tokens (one per email; used for Yes/No links)
CREATE TABLE IF NOT EXISTS `login_verification_tokens` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `login_event_id` int unsigned NOT NULL,
  `token` varchar(64) NOT NULL,
  `action` enum('yes','no') NOT NULL,
  `clicked_at` datetime DEFAULT NULL,
  `click_ip` varchar(45) DEFAULT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`),
  KEY `login_event_id` (`login_event_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 1.4 Security Trigger Logic (When to Send Email)
**Options:**
- **Demo mode:** Send on every login
- **Production:** Send when new IP or new device (compare with last known)

**For 3hr demo:** Use **every login**.

---

## ⏱️ HOUR 2 — Email + Tracking (60 mins)

### 2.1 Email Service Class
**New file:** `app/services/SecurityMailer.php`

Responsibilities:
- Send HTML email via API (MailerSend/Brevo) or SMTP (PHPMailer)
- Build email body with device, IP, time
- Generate tracking URLs for Yes/No buttons

### 2.2 Models
**New files:**
- `app/models/LoginEvent.php` — create, find by id
- `app/models/LoginVerificationToken.php` — create, findByToken, markClicked

### 2.3 Tracking URLs
- Base URL: `APP_URL` from config
- Yes: `{APP_URL}/auth/verify-login?token=XXX&action=yes`
- No:  `{APP_URL}/auth/verify-login?token=XXX&action=no`

Token = 64-char random hex (secure, unique).

### 2.4 Email HTML Template
**New file:** `app/views/emails/security_login.php`

Content:
- Greeting
- "You logged in to Aurave at {time} from {IP}."
- Device: {user_agent summary}
- Two buttons (links): Yes / No
- Expiry note: "Links expire in 10 minutes."

---

## ⏱️ HOUR 3 — Integration + Testing (60 mins)

### 3.1 AuthController Changes
**File:** `app/controllers/AuthController.php`

After successful login (before session set and redirect):
1. Create `login_event` (user_id, IP, user_agent)
2. Generate 2 tokens (yes, no), expiry = now + 10 min
3. Insert into `login_verification_tokens`
4. Call `SecurityMailer::sendLoginAlert($user, $loginEvent, $tokens)`
5. Continue with existing session/redirect (no blocking)

### 3.2 Verify-Login Controller
**New method** in `AuthController` or **new** `VerifyLoginController`:

**Route:** `GET /auth/verify-login` (no auth required)

Logic:
1. Get `token` and `action` from query
2. Validate token exists, not expired, not already clicked
3. Update `login_verification_tokens`: set clicked_at, click_ip
4. Show simple page: "Thank you. Your response has been recorded."
5. (Optional) If action=no: flag for review, could force logout

### 3.3 Routes
Add to `app/routes.php` (outside auth group):
```php
$router->get('/auth/verify-login', 'AuthController@verifyLogin');
```

### 3.4 Logs / Admin View (Optional)
- Add `/dashboard/security-logs` for Super Admin to see login_events + verification results
- Or query DB directly for demo

---

## 📁 FILES TO CREATE
| File | Purpose |
|------|---------|
| `database/migrations/security_mailer.sql` | DB tables |
| `app/services/SecurityMailer.php` | Email sending |
| `app/models/LoginEvent.php` | Login event model |
| `app/models/LoginVerificationToken.php` | Token model |
| `app/views/emails/security_login.php` | Email HTML |
| `app/views/auth/verify_result.php` | Thank-you page after click |

## 📁 FILES TO MODIFY
| File | Change |
|------|--------|
| `.env.example` | Add MAILER_* and SMTP vars |
| `app/controllers/AuthController.php` | Hook login → send email; add verifyLogin() |
| `app/routes.php` | Add verify-login route |
| `config/app.php` or new `config/mail.php` | Load mail config |

---

## 🔧 DEPENDENCIES
- **Option A (API):** Guzzle HTTP or cURL for MailerSend/Brevo API
- **Option B (SMTP):** PHPMailer via Composer: `composer require phpmailer/phpmailer`

**Recommendation for 3hr:** Use **Brevo SMTP + PHPMailer** — less setup, works everywhere.

---

## ✅ TESTING CHECKLIST
1. [ ] Run migration
2. [ ] Set .env with real SMTP/API keys
3. [ ] Log in to Aurave
4. [ ] Check Gmail inbox — email received
5. [ ] Open email — provider shows "opened"
6. [ ] Click "Yes, this was me" — lands on thank-you page
7. [ ] Check DB: `login_verification_tokens.clicked_at` and `click_ip` populated
8. [ ] Click "No" on another login — verify same flow

---

## 🔐 SECURITY TALKING POINTS (For Demo/Defense)
- Account takeover detection
- Behavioral monitoring (IP/device)
- Token-based verification (expiry, single-use)
- Event logging (login_events, click logs)
- Zero-trust login alerts
