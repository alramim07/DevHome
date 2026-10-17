# DevHome — Security, Performance & Free Hosting Design

## Intent

Make the DevHome agency site production-ready: hardened against common web
attacks, fast to load ("butter smooth"), and deployable for free with
automatic deploys from GitHub.

## Why now

The site currently runs locally (`php artisan serve`, `APP_DEBUG=true`) and
has never been hardened for the public internet. Several public POST
endpoints are unthrottled and one accepts unauthenticated file uploads.

## Goals

- Public form/mail endpoints cannot be abused to spam or flood; only
  syntactically valid, deliverable-looking email addresses can submit.
- SQL injection and stored-XSS are provably not possible via user input.
- Brute-force attempts against forms and admin login are rate limited.
- Production config leaks nothing (`APP_DEBUG=false`, no `.env` in git).
- Home page loads fast: cached queries, compiled framework caches, lazy
  images, no render-blocking regressions.
- One-command free deploy with GitHub auto-deploy on push.

## Non-goals

- Redesigning the UI or changing any existing interaction/animation.
- Rebuilding the frontend theme bundle (`public/front/js/bundle.js`).
- Adding a paid WAF/CDN.
- Moving the app to a different framework.

## Anti-goals (what would make this a failure)

- Any change that breaks the existing scroll animations, nav active state,
  contact form AJAX, or admin panel.
- Introducing a database portability bug (e.g. `LOWER(json)` failing on
  Postgres) that only shows up after deploy.
- Deploy instructions that require paid services.

## Constraints (from the codebase)

- Laravel 12, PHP 8.2, MySQL. `SESSION_DRIVER`, `CACHE_STORE`,
  `QUEUE_CONNECTION` all use the `database` driver.
- `public/front/js/bundle.js` (159 KB) is a compiled webpack artifact with
  no source in-repo; must not be edited.
- `->json()` columns + `whereRaw('LOWER(tech_stack) LIKE ?')` means the app
  is MySQL-oriented. Switching to Postgres would require code changes and
  risks bugs → **keep MySQL**.
- CV PDF generation uses `spatie/browsershot` + Puppeteer (needs Chromium).

## Decisions

- **Hosting:** Render free Web Service via **Docker** (Render's native
  runtime list has no PHP), connected to the GitHub repo for auto-deploy.
- **Database:** keep MySQL, hosted free externally (TiDB Cloud Serverless
  or Aiven free MySQL). Render's free Postgres is only 256 MB and expires,
  and would force Postgres code changes — rejected.
- **Cold start:** document it (free Render services sleep; first hit is
  slow). Mitigation: free uptime pinger, or accept it.
- **Netlify:** not possible — Netlify runs no PHP and has no persistent
  database; only static/JS functions. Answer documented in DEPLOYMENT.md.
- **Security:** Laravel rate limiting, upload lock-down, security-headers
  middleware, production env template. No behaviour changes to the UI.
- **Performance:** short-TTL caching for the site-settings/team queries,
  `php artisan optimize` in the deploy entrypoint, image lazy-loading.
- **Frontend asset delivery:** leave compiled assets as-is.

## What's left to the builder

The implementation plan covers exact files, rules, middleware, Docker
layout, and tests. Exact throttle limits and cache TTLs are specified in
the plan; adjust only if a test/behaviour demands it.
