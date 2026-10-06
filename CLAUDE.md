# EZKIRA — Project Context for Claude Code

SME finance-monitoring web app (revenue, expenses, P&L, budget tracking) for Malaysian SME business owners. Live at **https://ezkira.com**.

## Stack

- **Backend:** Pure PHP 8.1, hand-rolled MVC (no framework)
- **DB:** MySQL via PDO, prepared statements only
- **Frontend:** TailwindCSS via **CDN** (`cdn.tailwindcss.com`, not a compiled build — do not introduce a build step without discussing it first), vanilla JS (no bundler)
- **Hosting:** Shared cPanel hosting
- **Deploy:** GitHub Actions → FTP → cPanel, triggered on every push to `master` (`.github/workflows/deploy.yml`)
- **Auth:** Session-based + Google OAuth2 (`GOOGLE_CLIENT_ID`/`GOOGLE_CLIENT_SECRET` in `config/config.php`)
- **WhatsApp Blast feature:** uses Fonnte API (`FONNTE_TOKEN` constant), not a WA_* constant naming

## Architecture

- `app/Core/Router.php` + `routes/web.php` → route table (manual, not auto-discovered)
- `controllers/` → one class per resource, extends `App\Core\Controller`
- `models/` → PDO queries, one class per table-ish concept
- `views/` → plain PHP templates, `views/layouts/main.php` is the shared shell, `views/layouts/partials/` has nav/footer/help-drawer
- `config/config.php` → **NOT in git** (`.gitignore`'d). Must be created manually on each environment (local XAMPP vs production). `config/database.php` reads from it and is in git.
- `assets/js/app.js` → single global JS file loaded on every page (dark mode toggle, user dropdown, flash auto-dismiss, `#add-sale` / `#add-expense` quick-add deep links)
- **App shell (UI revamp, Oct 2026):** `views/layouts/main.php` = desktop sidebar (`partials/nav.php`, `lg:` and up) + sticky topbar (`partials/topbar.php`, holds dark toggle + user/"More" dropdown) + mobile bottom tab bar (`partials/bottom-nav.php`, centre "+" quick-add sheet). `main.php`'s Tailwind config overrides the `gray` palette with warm green-tinted neutrals and adds `sage`, so any page using `gray-*`/`bg-white` cards inherits the cream theme automatically. Design: dark forest green + sage + cream, gold only for logo/small highlights, font Plus Jakarta Sans, big rounded cards, pill tabs.
- **Logo (Oct 2026):** geometric "ez" monogram — gold `e` (#D4A820) + white `z` on #163020, pure SVG paths (no font dependency). `assets/img/logo-mark.svg` and `logo.svg` are the same square icon. Favicons, apple-touch icon, PWA icons (`assets/img/icons/`), root `favicon.ico`, and `assets/site.webmanifest` are wired into every page via `views/layouts/partials/head-icons.php`. PNGs were rendered from the SVG with headless Chrome — regenerate them if the mark changes. The old admin "Branding" site-logo upload was removed on purpose (owner wants the official logo everywhere); a stale `site_logo` row in `settings` and old files in `uploads/logos/` may still exist on production but are no longer read.
- **Revamp is phased:** Phase 1 (shell + Dashboard) is done. Revenue, Expenses, Balance Sheet, Profile, Blast, and Auth pages still have their old inner markup — restyle them to match `views/dashboard/index.php` without changing any form/route/JS behaviour.
- BASE_PATH must be defined before requiring `config/config.php` — production's config uses it for error log paths. Any new bootstrap/CLI script needs `define('BASE_PATH', ...)` before the require.

## Plans & billing (Free / Pro via CHIP)

- **Rule:** key-in, dashboard, export and Balance Sheet are free for everyone. Only *storing receipts* is limited: Free = 20 stored receipts (deleting one frees a slot), Pro = 1 GB. Old receipts always stay viewable. Pro is RM5.70/month or RM57/year, **prepaid with no auto-renew**.
- **Launch:** `App\Core\Plan::BILLING_START` = 2026-11-01 (Malaysia time). Before that every account is treated as Pro. Paying early starts the period on 1 Nov; renewing while Pro extends from the current end.
- **Code:** `app/Core/Plan.php` (policy, limits, prices), `models/Billing.php` (pro_until, usage, payments), `controllers/BillingController.php` (`/pricing`, `/billing/checkout`, `/billing/return`, `/billing/chip-callback`), `app/Core/Chip.php` (CHIP Collect REST client), `views/billing/pricing.php`, dashboard notice in `views/layouts/partials/plan-notice.php`.
- **Payment safety:** never trust callback bodies — `settle()` re-fetches the purchase from CHIP with the secret key and checks id + `reference` (`EZK-<payment id>`) + status (`paid`/`cleared`/`settled`). Test-mode purchases (`is_test`) only count when `CHIP_TEST_MODE` is true. Activation is idempotent (row lock in `Billing::activatePayment`).
- **Config (server `config/config.php`, not in git):** `CHIP_BRAND_ID`, `CHIP_SECRET_KEY`, optional `CHIP_TEST_MODE`. Without the first two, the pricing page shows "payments open soon" and checkout is disabled.
- **Schema:** migration 009 (`users.pro_until`, `expense_receipts.size_bytes`, `payments`) is applied automatically on first use by `App\Core\Schema::ensureBilling()` (flag file `storage/logs/schema_009_billing.done`). If it fails, plan checks fail open (nobody gets blocked) and the error goes to the PHP error log.
- **Receipts:** photos are shrunk in the browser (`ezShrinkImages` in `assets/js/app.js`) and again on the server (`app/Core/ReceiptImage.php`, needs GD; falls back to storing the original). Allowed types: JPG/PNG/WebP/GIF/PDF, max 10 MB. `.user.ini` limits were raised from 3M/4M because larger phone photos used to fail silently or trigger a 419 (PHP drops the whole POST above `post_max_size`).

## Deploy details

- FTP deploy excludes: `.git`, `.github`, `.claude`, `uploads/profiles/**`, `uploads/receipts/**`, `uploads/blast/**`, `storage/logs/**`, `storage/sessions/**`, `config/config.php`, `.env*`, `node_modules`
- Protocol is plain `ftp` (not `ftps`) — known weakness, not yet fixed, needs cPanel host to confirm FTPS support first
- **Every code change must be committed AND pushed to `master` to actually reach production** — there is no separate staging. A fix sitting in the working tree does nothing; this has caused real confusion before (user reported a bug "still not fixed" when the fix was simply never pushed).
- `config/config.php` must be manually created and uploaded to the server once (see `SETUP.md` / `DEPLOYMENT.md`), then typically `chmod 444`'d so FTP overwrites don't clobber it, and so it never gets wiped by a deploy.

## Database

- `database/schema.sql` is the base schema but has historically drifted behind the migration files — when setting up a fresh DB, apply `schema.sql` **then every** `database/migration_*.sql` in numeric order. Migrations as of now: 002 (business type), 003 (Google auth / `google_id`), 004 (refunds/`entry_type`), 005 (capitals table), 006 (`payment_method`), 007 (blast tables), 008 (rename `revenue_targets.amount` → `target_amount`), 009 (billing: `pro_until`, `size_bytes`, `payments` — auto-applied, see Plans & billing).
- **Action needed on existing production DB:** migration 008 has been committed but has NOT been confirmed run on the live `ezkira.com` database yet. Until it is, `Revenue::getTarget()`/`setTarget()` will throw a SQL error (column `target_amount` doesn't exist on old installs). Run this once via phpMyAdmin:
  ```sql
  ALTER TABLE `revenue_targets`
      CHANGE COLUMN `amount` `target_amount` DECIMAL(15,2) NOT NULL DEFAULT 0.00;
  ```

## Known issues / tech debt (not yet fixed)

- **Budget % settings are global, not per-user** (`models/Setting.php` + `ExpenseController::index()`). If one user changes their opex/marketing/cogs budget %, it overwrites it for every other user. A `budget_pct` table with `user_id` exists in schema but is unused — this is the intended fix path, needs a real feature pass.
- **FTP deploy uses plain `ftp`**, not `ftps`/`sftp` — ask host if FTPS is supported before switching (don't just flip the workflow blind, confirm cPanel supports it first).
- **CSRF token is not rotated per-request** (`app/Core/CSRF.php`) — only rotated on login. Low priority, needs care before changing (easy to break forms if done wrong).
- `tools/setup_google_auth.php` is a one-time DB migration runner guarded by a hardcoded token (`?token=fikira-google-2025`). It's still tracked in the repo. **Verify it has actually been deleted from the live production server** — it should never stay live after use.

## Recently fixed (context for "why does the code look like this")

- IDOR bugs in `ExpenseController::delete()` / `receipt()` / `receiptFile()` — added `user_id` ownership checks so one user can't delete/view another user's expenses or receipts.
- `Revenue::periodWhere()` used `WEEK(sale_date, 1)` while `recentTransactions()` used `WEEK(sale_date, 3)` (ISO week, matching PHP's `date('W')`) — standardized on mode 3.
- Open redirect in `AuthController::switchLang()` via unvalidated `HTTP_REFERER` — now validated against `APP_URL`'s host before redirecting.
- `Session::destroy()` cookie clear was missing `samesite=Lax`, inconsistent with how the cookie was originally set.
- Mobile nav — the old hamburger went through several iterations (onclick JS → inline JS → CSS `peer-checked`) before landing on a **pure CSS checkbox+label toggle with a literal `<style>` block**, to avoid depending on Tailwind CDN JIT for variant classes and on JS event handling on mobile. The hamburger is now replaced by the bottom tab bar, but its "+" quick-add sheet keeps the same pattern (`#quick-add-toggle` in `partials/bottom-nav.php`). The checkbox is `position:fixed` on purpose — when it was `absolute`, focusing it made the page jump to the bottom. Don't switch it to a JS toggle without a strong reason.
- Dashboard charts: donut canvases now get an explicit CSS size (they used to render 2x on retina phones), the compare chart is exposed as `window.renderCompareChart` (it used to throw `renderCompare is not defined`, so dark-mode toggling never redrew it), and the flatpickr pickers use `disableMobile: true` (the native mobile fallback showed an empty `dd/mm/yyyy` and crashed the monthSelect plugin).

## Working conventions

- User communicates in **Bahasa Malaysia** mixed with English (rojak) — respond in kind unless asked otherwise.
- User is non-technical-leaning product owner, not a PHP developer — explain fixes in plain terms, don't assume they'll read a diff unprompted.
- **Always commit AND push to `master` after a fix** — a local-only edit is invisible to the user testing on `ezkira.com`. Confirm the push succeeded before telling the user to test.
- No automated test suite exists. Verification = manual testing on the live site or local XAMPP (`http://localhost:8001` via the PHP built-in server configured in `.claude/launch.json`), since MySQL often isn't running locally either — check before assuming you can browser-test.
