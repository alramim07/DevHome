# Deploying DevHome — free, no card, auto-deploy from GitHub

**Stack (all free, none require a payment card):**

| Piece | Service | Why |
|---|---|---|
| Web app | **Render** (free web service, Docker) | Auto-deploys on every `git push`, free HTTPS + custom domain. Uses the repo's `Dockerfile`. |
| Database | **Aiven Free MySQL** | Managed MySQL, free forever, backups, no card. Keeps the engine this app is built for. |
| File storage | **Supabase Storage** (S3-compatible) | Render's free disk is wiped on restart, so uploads must live elsewhere. |
| Keep-alive | **cron-job.org** (or UptimeRobot) | Pings the site so Render's free instance never sleeps. |

> Why not Northflank/Koyeb/Fly.io/Oracle? They all require a **card on
> file** (even for their free tiers). Render does not.

## Can I run it on Netlify? (short answer: no)

Netlify has **no PHP runtime** and no persistent database. It only serves
static files and JS serverless functions. DevHome is a dynamic Laravel app
(server-rendered pages, MySQL, forms writing to the database, an admin
panel), so it cannot run on Netlify — nor on Vercel's PHP-less runtime.

---

## Part A — Create the database (Aiven, free)

1. Sign up at <https://aiven.io> (GitHub/Google/email, **no card**).
2. **Create service → MySQL → plan: Free**.
3. When it's running, open the service and copy:
   - **Host**, **Port** (usually `3306`), **User** (usually `avnadmin`),
     **Password**, **Database** (usually `defaultdb`).
   - **CA certificate** → download `ca.pem` (or copy its contents). You'll
     paste it into Render. *(Aiven requires TLS, so the CA is mandatory.)*

## Part B — Create the file storage (Supabase, free)

1. Sign up at <https://supabase.com> (GitHub, **no card**). Pick the region
   closest to your Render region (e.g. Singapore).
2. Create a project. Then go to **Storage → New bucket**:
   - Name it `devhome`.
   - Toggle **Public bucket** on. Save.
3. Go to **Project Settings → Storage → S3 access keys** and click
   **Generate new keys**. Copy the **Access key ID** and **Secret**.
4. Note your two endpoints (replace `<ref>` with your project ref, found in
   the project URL `https://<ref>.supabase.co`):
   - **S3 endpoint:** `https://<ref>.supabase.co/storage/v1/s3`
   - **Public URL base:** `https://<ref>.supabase.co/storage/v1/object/public/devhome`

## Part C — Deploy the app (Render, free)

1. Sign up at <https://render.com> with **GitHub** (**no card**).
2. **New → Web Service** → connect the **`alramim07/DevHome`** repo.
3. Fill in:
   - **Name:** `devhome`
   - **Region:** match your database region as closely as possible.
   - **Branch:** `main`
   - **Runtime:** **Docker** (uses the repo `Dockerfile`)
   - **Instance type:** **Free**
   - **Health check path:** `/up`
   - **Auto-Deploy:** Yes
4. Add the environment variables below, then **Create Web Service**.

### Environment variables

| Key | Value |
|-----|-------|
| `APP_KEY` | run `php artisan key:generate --show` locally, paste the `base64:...` |
| `APP_URL` | your Render URL, e.g. `https://devhome.onrender.com` |
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `DB_CONNECTION` | `mysql` |
| `DB_HOST` | Aiven host |
| `DB_PORT` | Aiven port (`3306`) |
| `DB_DATABASE` | Aiven database (`defaultdb`) |
| `DB_USERNAME` | Aiven user |
| `DB_PASSWORD` | Aiven password |
| `DB_SSL_CA_PEM_B64` | the `ca.pem` contents, base64-encoded (or use `DB_SSL_CA_PEM` for raw PEM) |
| `SESSION_DRIVER` | `database` |
| `SESSION_SECURE_COOKIE` | `true` |
| `SESSION_ENCRYPT` | `true` |
| `CACHE_STORE` | `database` |
| `QUEUE_CONNECTION` | `database` |
| `LOG_LEVEL` | `warning` |
| `RUN_MIGRATIONS` | `true` |
| `PUBLIC_DISK_DRIVER` | `s3` |
| `AWS_ACCESS_KEY_ID` | Supabase S3 access key |
| `AWS_SECRET_ACCESS_KEY` | Supabase S3 secret |
| `AWS_DEFAULT_REGION` | `ap-southeast-1` (any value works for Supabase) |
| `AWS_BUCKET` | `devhome` |
| `AWS_ENDPOINT` | `https://<ref>.supabase.co/storage/v1/s3` |
| `AWS_URL` | `https://<ref>.supabase.co/storage/v1/object/public/devhome` |
| `AWS_USE_PATH_STYLE_ENDPOINT` | `true` |

The container entrypoint writes the CA to disk, points the MySQL PDO driver
at it, runs `php artisan migrate --force`, caches config/routes/views, then
starts nginx + php-fpm.

## Part D — Keep it awake (free)

Render free instances sleep after ~15 minutes idle (≈30–60 s cold start on
the next visit). Create a free monitor at <https://cron-job.org> (or
UptimeRobot) that requests your `APP_URL` **every 10 minutes**. That keeps
the site warm and stops the database from going idle too.

## Part E — Verify

- Open your Render URL — the site should render.
- Open `/login` and add your admin content (projects, team, settings).
- Upload an image in the admin panel, then redeploy from Render and confirm
  the image still shows — it proves Supabase storage is wired correctly.

## Custom domain (optional)

Render → your service → **Settings → Custom Domains** → add your domain and
follow the DNS instructions. TLS is issued automatically.

---

## CV / PDF generation (optional — needs Chromium)

`/cv/devhome/{slug}` uses `spatie/laravel-pdf` (Browsershot → headless
Chromium). The default `Dockerfile` is intentionally **light** and does NOT
bundle Chromium, so this route will error unless you add it.

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
need CV PDFs — and check it still fits Render's free 512 MB.

---

## Troubleshooting

- **Build fails** → check the build logs. Ensure the Dockerfile is at the
  repo root and the Vite (Node) stage succeeded.
- **Container exits / 502** → check runtime logs. Usually wrong `DB_*`
  values, so `php artisan migrate` fails (the entrypoint exits on failure).
- **`SSL connection error` on the database** → the Aiven CA is missing or
  wrong (`DB_SSL_CA_PEM_B64`).
- **500 on every page** → `APP_KEY` is missing.
- **Images 404 / disappear after redeploy** → the `AWS_*` storage variables
  are wrong, or `PUBLIC_DISK_DRIVER` isn't `s3`. The public bucket must be
  public, and `AWS_URL` must end with the bucket name.
- **Site is slow on first load** → the keep-alive ping (Part D) isn't set up.
