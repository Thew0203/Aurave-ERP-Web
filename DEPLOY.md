# Deploy Aurave ERP – Quick Guide

## Deploy from GitHub

The project includes a `Dockerfile`. Push to GitHub, then connect your repo to Railway or Render.

---

## Option A: Railway (Recommended)

1. Go to [railway.app](https://railway.app) and sign in with **GitHub**
2. Click **New Project**
3. Select **Deploy from GitHub repo** → choose **Thew0203/Aurave-ERP-Web**
4. Railway will detect the Dockerfile and start building
5. Click **+ New** → **Database** → **MySQL** to add a MySQL database
6. Click your **web service** → **Variables** tab
7. Add these variables (get DB values from the MySQL service by clicking it → Connect → copy the values):

   | Variable | Value |
   |----------|-------|
   | `APP_NAME` | Aurave ERP |
   | `APP_ENV` | production |
   | `APP_DEBUG` | 0 |
   | `APP_URL` | `https://your-app.up.railway.app` (use your Railway URL) |
   | `DB_HOST` | (from MySQL service, e.g. `roundhouse.proxy.rlwy.net`) |
   | `DB_NAME` | (from MySQL service) |
   | `DB_USER` | (from MySQL service) |
   | `DB_PASS` | (from MySQL service) |
   | `DB_CHARSET` | utf8mb4 |

8. Click **Settings** → **Networking** → **Generate Domain** to get your public URL
9. Import database: use Railway's MySQL console or connect with a MySQL client and run `database/schema.sql` then `database/seed.sql`
10. Open your URL — you should see the app. Login: `superadmin@system.local` / `password`

---

## Option B: Render

1. Go to [render.com](https://render.com) and sign in with **GitHub**
2. Click **New** → **Web Service**
3. Connect **Thew0203/Aurave-ERP-Web**
4. Render will detect the Dockerfile
5. Click **Add Environment Variable** and add: `APP_NAME`, `APP_ENV`, `APP_DEBUG`, `APP_URL`, `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`, `DB_CHARSET`
6. **MySQL:** Render doesn't offer free MySQL. Use external MySQL: [PlanetScale](https://planetscale.com) (free tier) or [Railway MySQL](https://railway.app) (create a free project, add MySQL, copy connection details)
7. Create the service; Render will build and deploy
8. Import `database/schema.sql` and `database/seed.sql` into your database
9. Open your Render URL

---

## Push the Dockerfile to GitHub

If you just added the Dockerfile, push it:

```bash
cd /c/HOPEXAMPPWORKS/htdocs/Aurave
git add Dockerfile .dockerignore DEPLOY.md
git commit -m "Add Dockerfile for deployment"
git push origin main
```

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
