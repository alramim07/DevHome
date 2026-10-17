# Security, Performance & Free-Deploy Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Harden the DevHome Laravel site against abuse, speed up the home page, and make it deployable for free from GitHub with auto-deploy.

**Architecture:** Add Laravel middleware (security headers, throttling), tighten existing FormRequests, lock down the one unauthenticated upload endpoint, add short-TTL caching for hot queries, and ship a Docker + Render blueprint for zero-touch deploys. No changes to existing UI behaviour or the compiled theme bundle.

**Tech Stack:** Laravel 12, PHP 8.2, MySQL, Vite 7 / Tailwind 4, Docker (php-fpm + nginx), Render, PHPUnit.

**Spec:** `docs/superpowers/specs/2026-10-17-security-performance-deploy-design.md`

## Global Constraints

- PHP `^8.2`, Laravel `^12.0` (from `composer.json`); do not change these.
- Keep MySQL as the DB connection — do not introduce Postgres-only code paths.
- Do not edit `public/front/js/bundle.js` or `public/front/css/style.css` (compiled artifacts).
- Preserve every existing UI interaction: scroll animations, nav active state, contact AJAX (`fetch` + `@csrf`), admin panel.
- Public routes stay publicly reachable; no auth added to front-end pages.
- Tests must pass on the SQLite `:memory:` config already in `phpunit.xml`.

## Review Focus

Inputs/conditions the spec implies but no task's tests directly cover, most likely to bite a real user first:

1. A genuine visitor with a valid email whose domain has an A record but no MX record — must still be accepted (Laravel's `email:rfc,dns` falls back to A).
2. A visitor who submits the contact form twice quickly on purpose (double-click) — the second should be blocked without showing a broken UI state.
3. A logged-in admin using the rich-text editor image upload — must keep working after the endpoint is locked to authenticated users.
4. A visitor on a slow connection loading the home page below the fold — images must not cause layout shift or block first paint.
5. A free Render service that has gone to sleep — `/up` health check and the first real request must still succeed once it wakes.

---

### Task 1: Security headers middleware

**Files:**
- Create: `app/Http/Middleware/SecurityHeaders.php`
- Modify: `bootstrap/app.php:13-15`
- Test: `tests/Feature/SecurityHeadersTest.php`

**Interfaces:**
- Produces: `App\Http\Middleware\SecurityHeaders::handle(Request $request, Closure $next): Response`, registered in the `web` middleware group via `$middleware->web(append: [...])`.

- [ ] **Step 1: Write the failing test**

```php
public function test_security_headers_are_present(): void
{
    $this->get('/')->assertHeader('X-Content-Type-Options', 'nosniff')
        ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
        ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=SecurityHeadersTest`
Expected: FAIL (header not present)

- [ ] **Step 3: Create the middleware**

Add `X-Content-Type-Options: nosniff`, `X-Frame-Options: SAMEORIGIN`, `Referrer-Policy: strict-origin-when-cross-origin`, and `Permissions-Policy: geolocation=(), microphone=(), camera=()` to every response. Register it by appending to the `web` group in `bootstrap/app.php`.

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test --filter=SecurityHeadersTest`
Expected: PASS

- [ ] **Step 5: Commit**

```bash
git add app/Http/Middleware/SecurityHeaders.php bootstrap/app.php tests/Feature/SecurityHeadersTest.php
git commit -m "feat(security): add security headers middleware"
```

---

### Task 2: Rate limiting for public mail/form endpoints

**Files:**
- Modify: `routes/web.php:19-30,54`
- Modify: `bootstrap/app.php` (register named limiter in `withMiddleware` via `RateLimiter::for` — put it in `AppServiceProvider::boot()` instead)
- Modify: `app/Providers/AppServiceProvider.php`
- Test: `tests/Feature/RateLimitTest.php`

**Interfaces:**
- Produces: named limiters `contact` (5/min per IP), `forms` (10/min per IP), `login` (6/min per email+IP).
- Consumes: existing route names `contact.submit`, `email.submit`, `team.request`, `login.post`.

- [ ] **Step 1: Write the failing test**

```php
public function test_contact_form_is_rate_limited(): void
{
    $payload = ['first_name'=>'A','last_name'=>'B','email'=>'a@example.com','message'=>'hi'];
    for ($i = 0; $i < 5; $i++) {
        $this->post(route('contact.submit'), $payload)->assertStatus(200);
    }
    $this->post(route('contact.submit'), $payload)->assertStatus(429);
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=RateLimitTest`
Expected: FAIL (6th request returns 200, not 429)

- [ ] **Step 3: Define limiters and attach them**

In `AppServiceProvider::boot()`, define `RateLimiter::for('contact', fn (Request $r) => Limit::perMinute(5)->by($r->ip()))`, `RateLimiter::for('forms', ...10/min...)`, and `RateLimiter::for('login', fn (Request $r) => Limit::perMinute(6)->by($r->input('email').'|'.$r->ip()))`. Attach `->middleware('throttle:contact')` to `contact.submit` and `email.submit`; `->middleware('throttle:forms')` to `team.request` and `project.image.upload`; `->middleware('throttle:login')` to `login.post`.

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test --filter=RateLimitTest`
Expected: PASS

- [ ] **Step 5: Commit**

```bash
git add app/Providers/AppServiceProvider.php routes/web.php tests/Feature/RateLimitTest.php
git commit -m "feat(security): rate limit public forms and login"
```

---

### Task 3: Lock down the unauthenticated image upload

**Files:**
- Modify: `routes/web.php:24`
- Modify: `app/Http/Controllers/HomeController.php:109-131`
- Test: `tests/Feature/EditorImageUploadTest.php`

**Interfaces:**
- Produces: `POST /project-image-upload` requires an authenticated user and accepts only `jpg,jpeg,png,webp,gif` ≤ 5 MB.

- [ ] **Step 1: Write the failing tests**

```php
public function test_guest_cannot_upload_editor_image(): void
{
    $this->post(route('project.image.upload'), [])->assertRedirect(route('login'));
}

public function test_authenticated_user_can_upload_an_image(): void
{
    Storage::fake('public');
    $user = User::factory()->create();
    $file = UploadedFile::fake()->image('shot.jpg');
    $this->actingAs($user)->post(route('project.image.upload'), ['upload' => $file])
        ->assertOk()->assertJsonPath('uploaded', 1);
}
```

- [ ] **Step 2: Run tests to verify they fail**

Run: `php artisan test --filter=EditorImageUploadTest`
Expected: FAIL (guest upload currently succeeds; image extension not validated)

- [ ] **Step 3: Implement**

Move `project.image.upload` inside the `auth` middleware group in `routes/web.php`. In `uploadEditorImage`, validate `upload => required|image|mimes:jpg,jpeg,png,webp,gif|max:5120` before storing, and keep the existing JSON response shape (`fileName`, `uploaded`, `url`).

- [ ] **Step 4: Run tests to verify they pass**

Run: `php artisan test --filter=EditorImageUploadTest`
Expected: PASS

- [ ] **Step 5: Commit**

```bash
git add routes/web.php app/Http/Controllers/HomeController.php tests/Feature/EditorImageUploadTest.php
git commit -m "fix(security): require auth and image mime for editor uploads"
```

---

### Task 4: Email validation + LIKE wildcard hardening

**Files:**
- Modify: `app/Http/Requests/ProjectContactRequest.php:28`
- Modify: `app/Services/ProjectService.php:20-21`
- Test: `tests/Feature/ContactValidationTest.php`

**Interfaces:**
- Produces: both contact forms require `email:rfc,dns`; `ProjectService::getFilteredProjects` escapes `%` and `_` in the filter.

- [ ] **Step 1: Write the failing tests**

```php
public function test_project_contact_rejects_invalid_email(): void
{
    $this->post(route('email.submit'), [
        'first_name'=>'A','last_name'=>'B','email'=>'not-an-email','message'=>'hi','project_id'=>1,
    ])->assertStatus(422)->assertJsonValidationErrors('email');
}

public function test_filter_wildcards_are_escaped(): void
{
    Projects::factory()->create(['tech_stack' => ['Laravel']]);
    $this->get('/?filter=%')->assertOk();
}
```

- [ ] **Step 2: Run tests to verify they fail**

Run: `php artisan test --filter=ContactValidationTest`
Expected: FAIL (invalid email accepted; `%` returns everything)

- [ ] **Step 3: Implement**

Change `ProjectContactRequest` `email` rule to `required|email:rfc,dns|max:255`. In `ProjectService`, escape LIKE metacharacters: `$term = str_replace(['\\','%','_'], ['\\\\','\\%','\\_'], $filter);` then `whereRaw('LOWER(tech_stack) LIKE ? ESCAPE ?', ['%'.$term.'%', '\\'])` — verify the SQLite test still passes (SQLite supports `ESCAPE`).

- [ ] **Step 4: Run tests to verify they pass**

Run: `php artisan test --filter=ContactValidationTest`
Expected: PASS

- [ ] **Step 5: Commit**

```bash
git add app/Http/Requests/ProjectContactRequest.php app/Services/ProjectService.php tests/Feature/ContactValidationTest.php
git commit -m "fix(security): validate deliverable email and escape LIKE wildcards"
```

---

### Task 5: Production hardening config (session cookies, HTTPS, debug)

**Files:**
- Modify: `app/Providers/AppServiceProvider.php` (`boot()` → `URL::forceScheme('https')` when `app()->isProduction()`)
- Create: `.env.production.example`
- Modify: `docs/DEPLOYMENT.md` (created in Task 8)
- Test: `tests/Feature/ProductionConfigTest.php`

**Interfaces:**
- Produces: `.env.production.example` with `APP_ENV=production`, `APP_DEBUG=false`, `SESSION_SECURE_COOKIE=true`, `SESSION_HTTP_ONLY=true`, `SESSION_SAME_SITE=lax`, `SESSION_ENCRYPT=true`, `LOG_LEVEL=warning`.

- [ ] **Step 1: Write the failing test**

```php
public function test_production_env_example_is_locked_down(): void
{
    $env = file_get_contents(base_path('.env.production.example'));
    $this->assertStringContainsString('APP_DEBUG=false', $env);
    $this->assertStringContainsString('SESSION_SECURE_COOKIE=true', $env);
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=ProductionConfigTest`
Expected: FAIL (file missing)

- [ ] **Step 3: Implement**

Create `.env.production.example` from `.env.example` with production values and blank secrets. Add the HTTPS force in `AppServiceProvider::boot()` guarded by `app()->isProduction()` and `!app()->runningInConsole()`.

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test --filter=ProductionConfigTest`
Expected: PASS

- [ ] **Step 5: Commit**

```bash
git add .env.production.example app/Providers/AppServiceProvider.php tests/Feature/ProductionConfigTest.php
git commit -m "feat(security): production env template and forced HTTPS"
```

---

### Task 6: Cache hot home-page queries

**Files:**
- Modify: `app/Services/HomeService.php:23-30`
- Modify: `app/Http/Controllers/Admin/WebsiteSettingsController.php:55`
- Modify: `app/Http/Controllers/Admin/ProfileController.php` (team list invalidation on profile change) — only if it writes team data
- Test: `tests/Feature/HomeCachingTest.php`

**Interfaces:**
- Produces: `Website_settings::first()` and `User::all()` served from cache under keys `site.settings` and `site.team`, TTL 600s, busted on settings/profile update.

- [ ] **Step 1: Write the failing test**

```php
public function test_home_settings_are_cached(): void
{
    Website_settings::create(['site_name' => 'Dev Home']);
    $this->get('/')->assertOk();
    Cache::shouldReceive('remember')->atLeast()->once();
    $this->get('/');
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=HomeCachingTest`
Expected: FAIL (no cache interaction)

- [ ] **Step 3: Implement**

Wrap the two queries in `Cache::remember('site.settings', 600, fn () => Website_settings::first())` and `Cache::remember('site.team', 600, fn () => User::all())`. Add `Cache::forget('site.settings')` after the settings update and `Cache::forget('site.team')` after profile/dev create/update/delete.

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test --filter=HomeCachingTest`
Expected: PASS

- [ ] **Step 5: Commit**

```bash
git add app/Services/HomeService.php app/Http/Controllers/Admin/WebsiteSettingsController.php app/Providers/AppServiceProvider.php tests/Feature/HomeCachingTest.php
git commit -m "perf: cache site settings and team list"
```

---

### Task 7: Front-end load-time polish

**Files:**
- Modify: `resources/views/front/layouts/master.blade.php` (preconnect for fonts/CDN only if used)
- Modify: image tags in `resources/views/front/layouts/home/*.blade.php` and `resources/views/front/partials/project_cards.blade.php` (add `loading="lazy" decoding="async"` to below-the-fold images; keep hero image eager/high-priority)

**Interfaces:**
- Produces: no layout/behaviour change; below-fold images lazy, hero image eager with `fetchpriority="high"`.

- [ ] **Step 1: Write the check**

Confirm the hero image element and one below-fold image element by grepping after the edit: hero has `fetchpriority="high"`, at least one card image has `loading="lazy"`.

- [ ] **Step 2: Implement** the attribute changes only — do not move or restructure markup.

- [ ] **Step 3: Verify in the browser**

Run the dev server, load `/`, confirm: hero image visible immediately, scrolling reveals below-fold images, and no console errors introduced by the change.

- [ ] **Step 4: Commit**

```bash
git add resources/views/front
git commit -m "perf: lazy-load below-fold images"
```

---

### Task 8: Docker + Render deploy artifacts and guide

**Files:**
- Create: `Dockerfile`
- Create: `.dockerignore`
- Create: `docker/nginx.conf`
- Create: `docker/entrypoint.sh`
- Create: `render.yaml`
- Create: `docs/DEPLOYMENT.md`

**Interfaces:**
- Produces: a Docker image serving Laravel via php-fpm+nginx on `$PORT`; `render.yaml` blueprint with a web service, env vars, and a health check on `/up`; `DEPLOYMENT.md` with step-by-step GitHub→Render setup, free MySQL options, cold-start note, and the Netlify answer.

- [ ] **Step 1: Write `Dockerfile`**

Base `php:8.2-fpm-alpine`; install nginx, pdo_mysql, and Chromium deps for Browsershot; `composer install --no-dev --optimize-autoloader`; `npm ci && npm run build`; copy `docker/` configs; expose `$PORT`.

- [ ] **Step 2: Write `docker/nginx.conf`**

Server block listening on `${PORT}`, root `public/`, `try_files $uri $uri/ /index.php?$query_string`, fastcgi to `127.0.0.1:9000`, long cache headers for `/build/*` and static assets.

- [ ] **Step 3: Write `docker/entrypoint.sh`**

`set -e`; symlink storage; `php artisan migrate --force`; `php artisan optimize`; start php-fpm and nginx.

- [ ] **Step 4: Write `render.yaml`**

```yaml
services:
  - type: web
    name: devhome
    runtime: docker
    plan: free
    healthCheckPath: /up
    envVars:
      - key: APP_ENV
        value: production
      - key: APP_DEBUG
        value: "false"
      - key: APP_KEY
        sync: false
      - key: DB_CONNECTION
        value: mysql
      - key: DB_HOST
        sync: false
      - key: DB_PORT
        value: "3306"
      - key: DB_DATABASE
        sync: false
      - key: DB_USERNAME
        sync: false
      - key: DB_PASSWORD
        sync: false
      - key: SESSION_DRIVER
        value: database
      - key: CACHE_STORE
        value: database
      - key: QUEUE_CONNECTION
        value: database
      - key: LOG_LEVEL
        value: warning
```

- [ ] **Step 5: Verify the image builds and boots locally**

Run: `docker build -t devhome .` then `docker run -e PORT=8080 -e APP_KEY=... devhome`
Expected: build succeeds; `GET http://localhost:8080/up` returns 200.

- [ ] **Step 6: Write `docs/DEPLOYMENT.md`**

Cover: (a) push to GitHub → connect repo on Render → blueprint auto-detects `render.yaml`; (b) free MySQL via TiDB Cloud Serverless or Aiven; (c) set `APP_KEY` with `php artisan key:generate --show`; (d) cold-start explanation + optional free uptime pinger; (e) Netlify is not possible (no PHP runtime, no persistent DB) — static-only alternative noted; (f) Cloudflare free CDN for global speed.

- [ ] **Step 7: Commit**

```bash
git add Dockerfile .dockerignore docker/ render.yaml docs/DEPLOYMENT.md
git commit -m "feat(deploy): docker + render blueprint and deployment guide"
```

---

### Task 9: Full verification

**Files:**
- Test: whole suite

- [ ] **Step 1: Run the full test suite**

Run: `php artisan test`
Expected: all tests PASS, no failures.

- [ ] **Step 2: Browser smoke test**

Load `/` (home), `/team/{slug}`, `/login`, and submit the contact form once. Confirm: nav active-state still works, scroll animations still fire, contact AJAX success message appears, no new console errors.

- [ ] **Step 3: Security spot-check**

Confirm `/project-image-upload` is not reachable as a guest; a 6th rapid contact submission returns 429; response headers from Task 1 are present.

- [ ] **Step 4: Commit any fixes**

```bash
git add -A
git commit -m "test: verify security and performance hardening"
```

---

## Self-Review

- **Spec coverage:** headers (T1), rate limiting incl. login (T2), upload lock-down (T3), email validity + SQLi/LIKE (T4), prod env + HTTPS (T5), speed via caching (T6) + front-end (T7) + `artisan optimize` (T8), free GitHub deploy + Netlify answer (T8). All covered.
- **Type consistency:** named limiters (`contact`, `forms`, `login`) and cache keys (`site.settings`, `site.team`) are used consistently across T2/T6.
- **Review Focus mapping:** (1) email A-record fallback → T4 tests use `example.com`; (2) double-submit → T2; (3) admin editor upload → T3; (4) below-fold images → T7; (5) cold start → T8 health check.
- **Proportion:** plan is design-level; bodies only where the spec fixes exact values (limits, env keys, render.yaml).
