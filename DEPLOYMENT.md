# Deployment

This app is a PHP/MySQL application. Netlify cannot run it as-is because Netlify deploys static sites and serverless functions in JavaScript, TypeScript, and Go, not PHP runtime apps.

## Recommended: Railway

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

That checks the database connection and imports `database/schema.sql` and `database/seed.sql` when needed.

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
