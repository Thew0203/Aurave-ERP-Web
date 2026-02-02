# Deploy Aurave ERP – Quick Guide

## 1. Push to GitHub (Do This First)

```powershell
cd c:\HOPEXAMPPWORKS\htdocs\Aurave

# Init git (if not already)
git init

# Add all files
git add .

# Commit
git commit -m "Initial commit: Aurave ERP"

# Create repo on GitHub: https://github.com/new
# Name: Aurave (or aurave-erp)
# Then:

git remote add origin https://github.com/YOUR_USERNAME/Aurave.git
git branch -M main
git push -u origin main
```

---

## 2. Deployment Options (PHP + MySQL)

**Important:** Aurave is a PHP + MySQL app. Vercel supports PHP via serverless, but it's designed for static/JAMstack. For a traditional PHP app with MySQL and sessions, these work better:

### Option A: Railway (Recommended – Fastest)

1. Go to [railway.app](https://railway.app)
2. Sign in with GitHub
3. **New Project** → **Deploy from GitHub** → select your Aurave repo
4. Add **MySQL** service (Railway provides free MySQL)
5. Set env vars: `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`, `APP_URL`
6. Deploy

### Option B: Render

1. [render.com](https://render.com) → Sign in with GitHub
2. **New** → **Web Service** → connect repo
3. Environment: **PHP**
4. Add **MySQL** (or use external DB like PlanetScale)
5. Set env vars and deploy

### Option C: Vercel + PHP (Experimental)

Vercel can run PHP via [vercel-community/php](https://github.com/vercel-community/php). You need:

- `vercel.json` config
- MySQL hosted elsewhere (PlanetScale, Railway, etc.)
- Session handling may need adjustment (serverless = stateless)

Use this only if you're comfortable with PHP serverless. Railway/Render are simpler for this app.

---

## 3. Environment Variables (Production)

Set these on your host:

```
APP_NAME=Aurave ERP
APP_ENV=production
APP_DEBUG=0
APP_URL=https://your-app-url.com

DB_HOST=your-db-host
DB_NAME=aurave_erp
DB_USER=your-db-user
DB_PASS=your-db-password
DB_CHARSET=utf8mb4
```

---

## 4. Database Setup (Production)

1. Create MySQL database on your host
2. Import `database/schema.sql`
3. Import `database/seed.sql` (creates Super Admin)
4. Default login: `superadmin@system.local` / `password` (change immediately!)
