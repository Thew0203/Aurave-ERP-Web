# Security Policy

## Supported Versions

We release security updates for the current major version. Older versions are not supported.

## Reporting a Vulnerability

**Do not report security vulnerabilities in public issues, discussions, or pull requests.**

If you believe you have found a security vulnerability:

1. **Email** the maintainers (see repository owner/contacts) with a clear description of the issue and steps to reproduce.
2. **Do not** disclose the vulnerability publicly until it has been addressed.
3. Allow reasonable time for a fix before any public disclosure.
4. We will acknowledge receipt and work with you to confirm the issue and scope.

## What Not to Disclose Publicly

- Active credentials, API keys, or secrets (even if you believe they are test/demo).
- Unpatched vulnerability details that could be exploited before a fix is released.
- Personal data of any user or third party.

## Security Practices in This Project

- **Secrets:** All secrets (DB passwords, API keys, SMTP) belong in `.env`, which is gitignored. Use `.env.example` as a template only.
- **Passwords:** Stored with `password_hash()` (bcrypt). No plain-text or default production passwords.
- **Database:** PDO with prepared statements only; no raw SQL concatenation with user input.
- **Output:** User-supplied data is escaped with `htmlspecialchars()` in views to prevent XSS.
- **Multi-tenant:** All tenant data is scoped by `company_id`; no cross-tenant access.
- **Production:** Set `APP_DEBUG=0` and `APP_ENV=production`; do not expose stack traces or `phpinfo()`.

Thank you for helping keep this project safe.
