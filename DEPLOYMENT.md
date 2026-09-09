# Deployment

This project supports seamless deployment on **Vercel** with **Supabase (PostgreSQL)** backend as well as traditional shared hosting / Railway / local XAMPP.

## Recommended Option: Vercel + Supabase

### 1. Supabase Setup (PostgreSQL Database)
1. Log in to [Supabase](https://supabase.com) and create a new project.
2. Go to **SQL Editor** in your Supabase project dashboard.
3. Copy the contents of [`database/schema_pgsql.sql`](file:///c:/xampp/htdocs/univ-elearning/database/schema_pgsql.sql) into the SQL Editor and click **Run**.
4. In your Supabase project settings under **Database** -> **Connection String**, copy the **Transaction Connection Pooler** string (or URI format: `postgres://postgres.[ref]:[password]@aws-0-[region].pooler.supabase.com:6543/postgres`).

### 2. Vercel Setup (PHP Serverless Hosting)
1. Push this repository to GitHub.
2. Log in to [Vercel](https://vercel.com) and click **Add New Project** -> Select your GitHub repository.
3. In **Environment Variables**, add:
   - `DATABASE_URL` = Your Supabase Connection String
   - `APP_SECRET` = A strong random secret string (used to encrypt session cookies)
4. Click **Deploy**.
5. Once deployed, navigate to `https://your-vercel-app.vercel.app/deploy_setup.php` to verify database health and schema status.

---

## Alternative Options

For this codebase, free shared PHP hosting is the best fit.

### Option 1: InfinityFree

- Official site: https://www.infinityfree.com/
- Supports PHP 8.3, MySQL/MariaDB, free SSL, and custom domains.
- Best when you want fully free PHP hosting and can tolerate basic shared-hosting limits.

### Option 2: AwardSpace

- Official site: https://www.awardspace.com/free-hosting/
- Supports PHP and MySQL on a free shared-hosting plan.
- Best when you want a simpler control panel and a smaller starter site.

### Option 3: x10Hosting

- Official site: https://x10hosting.com/
- Offers free hosting with PHP and MySQL support.
- Best as a backup option if the first two do not fit your signup or region.

## Shared Hosting Setup

1. Create the free hosting account.
2. Create a MySQL database from the hosting control panel.
3. Copy [hosting.local.php.example](C:\xampp\htdocs\univ_elearning\config\hosting.local.php.example) to `config/hosting.local.php`.
4. Fill in the database host, database name, username, password, and site URL from the hosting provider.
5. Upload the project files to the hosting document root such as `htdocs` or `public_html`.
6. Open `https://your-site.example.com/deploy_setup.php`.

That page checks the database connection and imports `database/schema.sql` and `database/seed.sql` when needed.

## Optional: Railway

1. Push this repository to GitHub.
2. In Railway, create a new project from the GitHub repo.
3. Add a MySQL database service to the same Railway project.
4. In the web service variables, set:

```text
BASE_URL=https://your-railway-app-url/
```

Railway's MySQL service exposes variables such as `MYSQLHOST`, `MYSQLPORT`, `MYSQLDATABASE`, `MYSQLUSER`, and `MYSQLPASSWORD`. The app now reads those automatically. It also supports `DATABASE_URL`, `MYSQL_URL`, or the existing `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, and `DB_PASS` variables.

5. Deploy the web service.
6. Open:

```text
https://your-railway-app-url/deploy_setup.php
```

## Local Environment

For XAMPP, no environment variables are required. The app still defaults to:

```text
DB_HOST=localhost
DB_PORT=3306
DB_NAME=univ_elearning
DB_USER=root
DB_PASS=
BASE_URL=http://localhost/univ_elearning/
```

## About Netlify

You can use Netlify only for a separate static frontend, or as a static landing page that links to this PHP app hosted elsewhere. The current repo renders pages through PHP controllers and connects directly to MySQL, so a full Netlify-only deploy would require rebuilding the backend as Netlify Functions or moving the app to a JavaScript/API architecture.
