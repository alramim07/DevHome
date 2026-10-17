# Deploying DevHome (free, auto-deploy from GitHub)

Primary guide: **Northflank Sandbox** (free, always-on, includes a free
MySQL database). Render is documented as an alternative at the bottom.

## Can I run it on Netlify? (short answer: no)

Netlify has **no PHP runtime** and no persistent database. It only serves
static files and JavaScript serverless functions. DevHome is a dynamic
Laravel app (server-rendered pages, MySQL, forms writing to the database,
an admin panel), so it cannot run on Netlify — nor on Vercel's PHP-less
runtime. Only a fully static export of the public marketing pages could go
on Netlify, and even then your forms and admin panel would need a separate
backend. **Use Northflank.**

## Why Northflank over Render

| | Render (free) | **Northflank Sandbox (free)** |
|---|---|---|
| Sleeps when idle | Yes (~15 min → 30–60 s cold start) | **No — always on** |
| Free database | Postgres only, and it expires | **1 free DB, MySQL supported** |
| Push-to-deploy | Yes | Yes |
| Docker | Yes | Yes |

Northflank's free "Developer Sandbox" plan gives you **2 services, 1
database, and always-on compute**, so the site never cold-starts and you
get MySQL without a third-party provider.

> If you later outgrow the sandbox (more CPU/RAM/instances), Northflank
> requires upgrading to a paid plan.

## Step-by-step (Northflank)

### 1. Push this repo to GitHub

```bash
git add -A
git commit -m "feat: production hardening + free deploy config"
git push origin main
```

### 2. Create your Northflank account

Go to <https://app.northflank.com/signup> and sign up with **GitHub**
(that also links your repositories).

### 3. Create a project

Dashboard → **Create new** → **Project**. Name it e.g. `devhome`.

### 4. Create the MySQL database

In the project: **Create new → Database → MySQL** (name it `db`). Pick the
free/sandbox-compatible plan. When it's up, open it and copy its
**connection details**: host, port, database name, username, password.

### 5. Create the web service

In the same project: **Create new → Service → Combined**.

- **Repository:** `lynsromero/DevHome`, **branch** `main`.
- **Build:** choose **Dockerfile** (it's at the repo root; leave the build
  context at `/`).
- **Networking:** Northflank auto-detects the port from the Dockerfile
  (`EXPOSE 10000`). If it doesn't, add a port: **10000**, protocol
  **HTTP**, accessibility **Public**, name e.g. `web`. That gives you a
  public URL like `https://<name>-<project>.code.run`.
- **Resources:** leave on the default sandbox values.
- **Advanced / Health check:** set the path to **`/up`**.

Click **Create service** — it starts building immediately.

### 6. Add environment variables & secrets

Service → **Configure → Variables & secrets** (or a linked secret group).
Add:

| Key | Value |
|-----|-------|
| `APP_KEY` | `php artisan key:generate --show` (run locally, paste the `base64:...` value) |
| `APP_URL` | your Northflank public URL, e.g. `https://web-devhome.code.run` |
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `DB_CONNECTION` | `mysql` |
| `DB_HOST` | from step 4 |
| `DB_PORT` | from step 4 (usually `3306`) |
| `DB_DATABASE` | from step 4 |
| `DB_USERNAME` | from step 4 |
| `DB_PASSWORD` | from step 4 |
| `SESSION_DRIVER` | `database` |
| `SESSION_SECURE_COOKIE` | `true` |
| `SESSION_ENCRYPT` | `true` |
| `CACHE_STORE` | `database` |
| `QUEUE_CONNECTION` | `database` |
| `LOG_LEVEL` | `warning` |
| `RUN_MIGRATIONS` | `true` |

> Northflank can also inject the database's own variables when you link the
> DB to the service — but the names must match what the app reads, so the
> explicit `DB_*` list above is the reliable route.

Save, then **restart/redeploy** the service.

### 7. Verify

The container entrypoint runs `php artisan migrate --force` and caches
config/routes/views on boot. Once live:

- Open your public URL — the site should render.
- Open `/login` and create your admin content (projects, team, settings).

### 8. Auto-deploy

CI/CD is **on by default**: every `git push` to `main` triggers a rebuild
and redeploy. Nothing else to configure.

## Speed tips

- Static assets (`/build/*`, theme CSS/JS) are served with long-lived cache
  headers by nginx; Northflank serves them over HTTPS/CDN.
- The home page caches its settings/team queries for 10 minutes
  (`site.settings`, `site.team`), busted automatically on admin updates.
- Add **Cloudflare** (free) in front of a custom domain for extra edge
  caching and DDoS protection.

## CV / PDF generation (optional — needs Chromium)

`/cv/devhome/{slug}` uses `spatie/laravel-pdf` (Browsershot → headless
Chromium). The default `Dockerfile` is intentionally **light** and does NOT
bundle Chromium, so this route will error in production unless you add it.

To enable CV PDFs, add to the `FROM php:8.2-fpm-alpine` stage:

```dockerfile
RUN apk add --no-cache chromium chromium-chromedriver
ENV CHROME_PATH=/usr/bin/chromium-browser
```

and point Browsershot at it (in `AppServiceProvider::boot()`):

```php
if ($path = env('CHROME_PATH')) {
    \Spatie\Browsershot\Browsershot::setChromePath($path);
}
```

Then redeploy. This grows the image and uses more RAM, so only add it if you
need CV PDFs.

## Alternative: Render (free)

`render.yaml` is included if you'd rather use Render. It uses the same
`Dockerfile` and `/up` health check, but: free services **sleep after ~15
min idle**, and Render's free database is Postgres (which this app is not
tuned for), so you'd host MySQL externally on **TiDB Cloud Serverless** or
**Aiven** and set the `DB_*` variables in the Render dashboard. Everything
else — `APP_KEY`, `APP_URL`, secrets — is the same list as step 6.

## Troubleshooting

- **Build fails** → check the build logs. Ensure the Dockerfile is at the
  repo root and the Vite build (Node stage) succeeded.
- **Container exits / 502** → check runtime logs. Usually wrong `DB_*`
  values, so `php artisan migrate` fails (the entrypoint exits on failure).
- **500 on every page** → `APP_KEY` is missing.
- **Logos/images 404** → `storage:link` runs in the entrypoint; make sure
  content was uploaded through the admin panel.
